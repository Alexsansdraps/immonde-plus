/**
 * ImmondeFX — petit moteur d'effets partagé par tous les jeux d'Imm🧅nde Plus.
 *
 * - Sons générés à la volée via WebAudio (aucun fichier audio à charger).
 * - Vibration mobile.
 * - High scores persistés en localStorage.
 *
 * Usage :
 *   ImmondeFX.unlock();              // à appeler sur le 1er clic (geste utilisateur)
 *   ImmondeFX.play('eat');           // 'eat' | 'bonus' | 'malus' | 'death' | 'hit' | 'success' | 'error' | 'shoot' | 'pop'
 *   ImmondeFX.vibrate(20);
 *   ImmondeFX.getHighScore('snake'); // lit le record
 *   ImmondeFX.setHighScore('snake', 120, 'max'); // 'max' (plus haut = mieux) ou 'min' (plus bas = mieux)
 */
(function () {
    if (window.ImmondeFX) return;

    let ctx = null;
    let muted = false;

    function ensureCtx() {
        if (!ctx) {
            try { ctx = new (window.AudioContext || window.webkitAudioContext)(); }
            catch (e) { ctx = null; }
        }
        return ctx;
    }

    // À brancher sur un geste utilisateur (clic/tap) pour débloquer l'audio.
    function unlock() {
        const c = ensureCtx();
        if (c && c.state === 'suspended') c.resume();
    }

    // Un bip enveloppé : type d'oscillateur + glissando de fréquence + déclin.
    function tone({ type = 'sine', f0 = 440, f1 = f0, dur = 0.15, gain = 0.2, curve = 'exp' }) {
        const c = ensureCtx();
        if (!c || muted) return;
        const t = c.currentTime;
        const osc = c.createOscillator();
        const g = c.createGain();
        osc.connect(g); g.connect(c.destination);
        osc.type = type;
        osc.frequency.setValueAtTime(f0, t);
        if (curve === 'exp') osc.frequency.exponentialRampToValueAtTime(Math.max(1, f1), t + dur);
        else osc.frequency.linearRampToValueAtTime(f1, t + dur);
        g.gain.setValueAtTime(gain, t);
        g.gain.exponentialRampToValueAtTime(0.0001, t + dur);
        osc.start(t); osc.stop(t + dur);
    }

    const SOUNDS = {
        eat:     () => tone({ type: 'sawtooth', f0: 180, f1: 60,  dur: 0.18, gain: 0.25 }),
        pop:     () => tone({ type: 'triangle', f0: 520, f1: 120, dur: 0.10, gain: 0.22 }),
        bonus:   () => tone({ type: 'square',   f0: 440, f1: 880, dur: 0.20, gain: 0.20 }),
        success: () => { tone({ type: 'square', f0: 523, f1: 660, dur: 0.12, gain: 0.18 });
                         setTimeout(() => tone({ type: 'square', f0: 784, f1: 988, dur: 0.18, gain: 0.18 }), 110); },
        hit:     () => tone({ type: 'square',   f0: 300, f1: 90,  dur: 0.08, gain: 0.25 }),
        shoot:   () => tone({ type: 'triangle', f0: 700, f1: 300, dur: 0.09, gain: 0.18 }),
        malus:   () => tone({ type: 'sawtooth', f0: 90,  f1: 45,  dur: 0.30, gain: 0.30, curve: 'lin' }),
        error:   () => tone({ type: 'sawtooth', f0: 160, f1: 70,  dur: 0.25, gain: 0.28, curve: 'lin' }),
        death:   () => tone({ type: 'sawtooth', f0: 300, f1: 40,  dur: 0.70, gain: 0.35 }),
    };

    function play(name) {
        const s = SOUNDS[name];
        if (s) s();
    }

    function vibrate(pattern) {
        if (navigator.vibrate) { try { navigator.vibrate(pattern); } catch (e) {} }
    }

    // --- High scores ---
    const HS_PREFIX = 'immondeHS_';

    function getHighScore(game) {
        const v = localStorage.getItem(HS_PREFIX + game);
        return v === null ? null : parseInt(v, 10);
    }

    // mode 'max' : on garde le plus grand. mode 'min' : on garde le plus petit.
    // Renvoie true si un nouveau record a été enregistré.
    function setHighScore(game, score, mode = 'max') {
        const current = getHighScore(game);
        let isRecord;
        if (current === null) isRecord = true;
        else isRecord = mode === 'min' ? score < current : score > current;
        if (isRecord) localStorage.setItem(HS_PREFIX + game, String(score));
        return isRecord;
    }

    window.ImmondeFX = { unlock, play, vibrate, getHighScore, setHighScore, get muted() { return muted; }, set muted(v) { muted = !!v; } };
})();
