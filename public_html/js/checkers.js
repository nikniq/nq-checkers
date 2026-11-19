/* Checkers (American / English draughts) — 8x8, mandatory captures, multi-jumps, kings. */
(() => {
    'use strict';

    const RED = 'r', BLACK = 'b';
    const DRAW_PLIES = 80; // 40 moves each without a capture or promotion => draw
    const DEPTH = { easy: 1, medium: 3, hard: 6 };

    const cfg = window.CHECKERS || {};
    const $ = (id) => document.getElementById(id);
    const boardEl = $('board');

    let state, selected, partial, legal, busy, startedAt, finished, hint;

    // ---------- Rules engine (pure functions on a board array) ----------

    const inBounds = (r, c) => r >= 0 && r < 8 && c >= 0 && c < 8;
    const opponent = (color) => (color === RED ? BLACK : RED);
    const cloneBoard = (b) => b.map((row) => row.map((p) => (p ? { ...p } : null)));

    function initialBoard() {
        const b = Array.from({ length: 8 }, () => Array(8).fill(null));
        for (let r = 0; r < 8; r++) {
            for (let c = 0; c < 8; c++) {
                if ((r + c) % 2 !== 1) continue;
                if (r < 3) b[r][c] = { color: BLACK, king: false };
                if (r > 4) b[r][c] = { color: RED, king: false };
            }
        }
        return b;
    }

    function dirsFor(piece) {
        if (piece.king) return [[-1, -1], [-1, 1], [1, -1], [1, 1]];
        return piece.color === RED ? [[-1, -1], [-1, 1]] : [[1, -1], [1, 1]];
    }

    const crowns = (piece, r) => !piece.king && ((piece.color === RED && r === 0) || (piece.color === BLACK && r === 7));

    function jumpSequences(b, r, c, piece, path, captures, out) {
        let extended = false;
        for (const [dr, dc] of dirsFor(piece)) {
            const mr = r + dr, mc = c + dc, tr = r + 2 * dr, tc = c + 2 * dc;
            if (!inBounds(tr, tc) || b[tr][tc]) continue;
            const mid = b[mr][mc];
            if (!mid || mid.color === piece.color) continue;
            if (captures.some(([x, y]) => x === mr && y === mc)) continue;
            extended = true;
            const nb = cloneBoard(b);
            nb[tr][tc] = nb[r][c];
            nb[r][c] = null;
            const nextPath = [...path, [tr, tc]];
            const nextCaps = [...captures, [mr, mc]];
            if (crowns(piece, tr)) {
                out.push({ path: nextPath, captures: nextCaps }); // crowning ends the turn
            } else {
                jumpSequences(nb, tr, tc, piece, nextPath, nextCaps, out);
            }
        }
        if (!extended && captures.length) out.push({ path, captures });
    }

    function legalMoves(b, color) {
        const jumps = [], steps = [];
        for (let r = 0; r < 8; r++) {
            for (let c = 0; c < 8; c++) {
                const p = b[r][c];
                if (!p || p.color !== color) continue;
                jumpSequences(b, r, c, p, [[r, c]], [], jumps);
                for (const [dr, dc] of dirsFor(p)) {
                    const tr = r + dr, tc = c + dc;
                    if (inBounds(tr, tc) && !b[tr][tc]) steps.push({ path: [[r, c], [tr, tc]], captures: [] });
                }
            }
        }
        return jumps.length ? jumps : steps;
    }

    function applyMove(b, move) {
        const nb = cloneBoard(b);
        const [fr, fc] = move.path[0];
        const [tr, tc] = move.path[move.path.length - 1];
        const piece = nb[fr][fc];
        nb[fr][fc] = null;
        for (const [r, c] of move.captures) nb[r][c] = null;
        const promoted = crowns(piece, tr);
        if (promoted) piece.king = true;
        nb[tr][tc] = piece;
        return { board: nb, promoted };
    }

    function count(b, color) {
        let n = 0;
        for (const row of b) for (const p of row) if (p && p.color === color) n++;
        return n;
    }

    // ---------- AI: minimax with alpha-beta ----------

    function evaluate(b, color) {
        let score = 0;
        for (let r = 0; r < 8; r++) {
            for (let c = 0; c < 8; c++) {
                const p = b[r][c];
                if (!p) continue;
                let v = p.king ? 175 : 100;
                if (!p.king) v += (p.color === RED ? 7 - r : r) * 3; // advancement
                if (c >= 2 && c <= 5 && r >= 2 && r <= 5) v += 4; // centre control
                if (!p.king && ((p.color === RED && r === 7) || (p.color === BLACK && r === 0))) v += 6; // back row guard
                score += p.color === color ? v : -v;
            }
        }
        return score;
    }

    function minimax(b, depth, alpha, beta, color, maximizing, root) {
        const toMove = maximizing ? root : opponent(root);
        const moves = legalMoves(b, toMove);
        if (!moves.length) return maximizing ? -100000 - depth : 100000 + depth;
        if (depth === 0) return evaluate(b, root);
        if (maximizing) {
            let best = -Infinity;
            for (const m of moves) {
                best = Math.max(best, minimax(applyMove(b, m).board, depth - 1, alpha, beta, color, false, root));
                alpha = Math.max(alpha, best);
                if (beta <= alpha) break;
            }
            return best;
        }
        let best = Infinity;
        for (const m of moves) {
            best = Math.min(best, minimax(applyMove(b, m).board, depth - 1, alpha, beta, color, true, root));
            beta = Math.min(beta, best);
            if (beta <= alpha) break;
        }
        return best;
    }

    function chooseAiMove(b, color, difficulty) {
        const moves = legalMoves(b, color);
        if (difficulty === 'easy' && Math.random() < 0.35) return moves[Math.floor(Math.random() * moves.length)];
        const depth = DEPTH[difficulty] || 3;
        let bestScore = -Infinity, best = [];
        for (const m of moves) {
            const s = minimax(applyMove(b, m).board, depth - 1, -Infinity, Infinity, color, false, color);
            if (s > bestScore) { bestScore = s; best = [m]; } else if (s === bestScore) best.push(m);
        }
        return best[Math.floor(Math.random() * best.length)];
    }

    // ---------- Game flow / UI ----------

    const mode = () => $('mode').value;
    const difficulty = () => $('difficulty').value;
    const colorName = (c) => (c === RED ? 'Red' : 'Black');

    function newGame() {
        state = { board: initialBoard(), turn: RED, redMoves: 0, plies: 0, quietPlies: 0 };
        selected = null; partial = null; busy = false; finished = false; hint = null;
        startedAt = Date.now();
        $('result').hidden = true;
        $('mode').disabled = false; $('difficulty').disabled = false;
        refresh();
    }

    function refresh() {
        legal = finished ? [] : legalMoves(state.board, state.turn);
        render();
        updateStatus();
    }

    function updateStatus() {
        $('moves').textContent = state.redMoves;
        $('red-count').textContent = count(state.board, RED);
        $('black-count').textContent = count(state.board, BLACK);
        if (finished) return;
        const vsAi = mode() === 'ai';
        if (vsAi) $('status').textContent = state.turn === RED ? 'Your move (red)' : 'Computer is thinking…';
        else $('status').textContent = `${colorName(state.turn)} to move`;
    }

    function candidateMoves() {
        if (!partial) return [];
        return legal.filter((m) => partial.every(([r, c], i) => m.path[i] && m.path[i][0] === r && m.path[i][1] === c));
    }

    function render() {
        const movable = new Set(legal.map((m) => m.path[0].join(',')));
        const cands = candidateMoves();
        const targets = new Set(cands.filter((m) => m.path.length > partial.length).map((m) => m.path[partial.length].join(',')));
        const humanTurn = !finished && !busy && (mode() === 'local' || state.turn === RED);

        boardEl.innerHTML = '';
        for (let r = 0; r < 8; r++) {
            for (let c = 0; c < 8; c++) {
                const sq = document.createElement('div');
                sq.className = 'sq ' + ((r + c) % 2 ? 'dark' : 'light');
                sq.dataset.r = r; sq.dataset.c = c;
                const key = `${r},${c}`;
                if (targets.has(key)) sq.classList.add('target');
                if (hint && hint.path.some(([x, y]) => x === r && y === c)) sq.classList.add(hint.path[0][0] === r && hint.path[0][1] === c ? 'hint-from' : 'hint');
                if (partial && partial.some(([x, y]) => x === r && y === c)) sq.classList.add('trail');
                const p = state.board[r][c];
                if (p) {
                    const piece = document.createElement('div');
                    piece.className = `piece ${p.color === RED ? 'red' : 'black'}${p.king ? ' king' : ''}`;
                    if (humanTurn && !partial && movable.has(key)) piece.classList.add('movable');
                    if (selected && selected[0] === r && selected[1] === c) piece.classList.add('selected');
                    sq.appendChild(piece);
                }
                boardEl.appendChild(sq);
            }
        }
    }

    function onBoardClick(e) {
        const sq = e.target.closest('.sq');
        if (!sq || finished || busy) return;
        if (mode() === 'ai' && state.turn !== RED) return;
        const r = +sq.dataset.r, c = +sq.dataset.c;

        // Continue a partial (multi-jump) move
        if (partial) {
            const cands = candidateMoves();
            const next = cands.filter((m) => m.path[partial.length] && m.path[partial.length][0] === r && m.path[partial.length][1] === c);
            if (next.length) {
                partial = [...partial, [r, c]];
                const complete = next.find((m) => m.path.length === partial.length);
                if (complete) return commit(complete);
                selected = [r, c];
                return render();
            }
            if (partial.length > 1) return; // mid-jump: must finish the sequence
        }

        // (Re)select a piece
        if (legal.some((m) => m.path[0][0] === r && m.path[0][1] === c)) {
            selected = [r, c];
            partial = [[r, c]];
        } else {
            selected = null; partial = null;
        }
        render();
    }

    function commit(move) {
        const { board, promoted } = applyMove(state.board, move);
        if (state.turn === RED) state.redMoves++;
        state.plies++;
        state.quietPlies = move.captures.length || promoted ? 0 : state.quietPlies + 1;
        state.board = board;
        state.turn = opponent(state.turn);
        selected = null; partial = null; hint = null; $('hint-text').hidden = true;
        $('mode').disabled = true; $('difficulty').disabled = true;
        refresh();
        if (checkEnd()) return;
        if (mode() === 'ai' && state.turn === BLACK) aiTurn();
    }

    function aiTurn() {
        busy = true;
        render();
        setTimeout(() => {
            const move = chooseAiMove(state.board, BLACK, difficulty());
            busy = false;
            commit(move);
        }, 350);
    }

    function checkEnd() {
        let winner = null, draw = false;
        if (!legal.length) winner = opponent(state.turn);
        else if (state.quietPlies >= DRAW_PLIES) draw = true;
        if (!winner && !draw) return false;

        finished = true;
        render();
        let text;
        if (draw) text = 'Draw — 40 moves each without a capture.';
        else if (mode() === 'ai') text = winner === RED ? 'You win! 🎉' : 'The computer wins.';
        else text = `${colorName(winner)} wins!`;
        $('status').textContent = 'Game over';
        showResult(text);

        if (mode() === 'ai') {
            const result = draw ? 'draw' : winner === RED ? 'win' : 'loss';
            saveScore(result);
        }
        return true;
    }

    function showResult(html) {
        const el = $('result');
        el.innerHTML = html;
        el.hidden = false;
    }

    async function saveScore(result) {
        if (!cfg.loggedIn) {
            showResult($('result').innerHTML + '<br><small>Log in to save scores like this one.</small>');
            return;
        }
        const payload = {
            result,
            difficulty: difficulty(),
            moves: Math.max(1, state.redMoves),
            pieces_left: count(state.board, RED),
            captured: 12 - count(state.board, BLACK),
            duration_seconds: Math.round((Date.now() - startedAt) / 1000),
        };
        try {
            const res = await fetch(cfg.saveUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify(payload),
            });
            if (!res.ok) throw new Error(res.status);
            const data = await res.json();
            showResult($('result').innerHTML + `<br><small>Saved: +${data.points} points (total ${data.total}).</small>`);
        } catch (err) {
            showResult($('result').innerHTML + '<br><small>Could not save score.</small>');
        }
    }

    // ---------- Hints ----------

    const sqName = ([r, c]) => 'abcdefgh'[c] + (8 - r);

    function showHint() {
        const out = $('hint-text');
        if (finished || busy || (mode() === 'ai' && state.turn !== RED) || !legal.length) return;
        const move = chooseAiMove(state.board, state.turn, 'medium');
        hint = move;
        const { promoted } = applyMove(state.board, move);
        const route = move.path.map(sqName).join(' → ');
        let why;
        if (move.captures.length > 1) why = `Multi-jump! You capture ${move.captures.length} pieces in one turn.`;
        else if (move.captures.length) why = 'A capture is available, and captures are mandatory. This is the best one.';
        else if (promoted) why = 'This move reaches the far row and crowns a king.';
        else {
            const after = applyMove(state.board, move).board;
            const threatened = legalMoves(after, opponent(state.turn)).some((m) => m.captures.length);
            why = threatened ? 'Every move leaves something exposed. This one limits the damage.' : 'A safe move: the opponent cannot capture anything afterwards.';
        }
        out.innerHTML = `<strong>Hint:</strong> ${route}<br><span class="muted">${why}</span>`;
        out.hidden = false;
        render();
    }

    const TIPS = [
        'Keep your back row filled as long as you can, so the opponent struggles to crown kings.',
        'Control the centre squares. Pieces there have more options.',
        'Move pieces in pairs. A piece with a friend behind it can\'t be jumped.',
        'Pieces on the side edges can\'t be captured, but they also have fewer moves.',
        'Captures are mandatory. Use this to force your opponent into a bad jump.',
        'Trade pieces when you\'re ahead. Avoid trades when you\'re behind.',
        'Kings move backwards too. Crowning one early is a big advantage.',
        'Before each move, check which squares your opponent could jump to next.',
    ];
    let tipIdx = Math.floor(Math.random() * TIPS.length);
    function nextTip() { tipIdx = (tipIdx + 1) % TIPS.length; $('tip').textContent = TIPS[tipIdx]; }
    $('tip').textContent = TIPS[tipIdx];
    $('next-tip').addEventListener('click', nextTip);
    $('hint').addEventListener('click', showHint);

    boardEl.addEventListener('click', onBoardClick);
    $('new-game').addEventListener('click', newGame);
    $('mode').addEventListener('change', () => {
        $('difficulty-wrap').hidden = mode() !== 'ai';
        newGame();
    });
    $('difficulty').addEventListener('change', newGame);

    newGame();
})();
