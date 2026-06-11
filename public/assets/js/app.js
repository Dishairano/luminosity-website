// Interactieve demo: praat met de PHP-API, die op zijn beurt de Python-engine
// aanroept. Twee stappen, net als de echte schil: eerst vertalen/checken, dan
// (optioneel) uitvoeren in de sandbox.

(() => {
    const form   = document.getElementById('try-form');
    const invoer = document.getElementById('invoer');
    const status = document.getElementById('status');
    const kaart  = document.getElementById('kaart');
    const elTitel = document.getElementById('k-titel');
    const elCmd   = document.getElementById('k-commando');
    const elUitleg= document.getElementById('k-uitleg');
    const elVeil  = document.getElementById('k-veiligheid');
    const elHint  = document.getElementById('k-hint');
    const elRes   = document.getElementById('k-resultaat');
    const btnRun  = document.getElementById('btn-uitvoeren');

    let huidigeInvoer = '';

    function setStatus(tekst, bezig) {
        status.textContent = tekst;
        status.classList.toggle('bezig', !!bezig);
    }

    async function api(pad, invoerTekst) {
        const res = await fetch(pad, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ invoer: invoerTekst }),
        });
        return res.json();
    }

    async function vertaal(tekst) {
        huidigeInvoer = tekst;
        setStatus('bezig…', true);
        elRes.classList.add('hidden');
        btnRun.classList.add('hidden');
        elHint.textContent = '';

        let data;
        try { data = await api('/api/verwerk', tekst); }
        catch { setStatus('fout', false); return; }

        setStatus('klaar', false);
        kaart.classList.remove('hidden');

        if (!data.ok) {
            elTitel.textContent = 'Niet begrepen';
            elCmd.classList.add('hidden');
            elUitleg.textContent = data.fout || 'Onbekende fout.';
            elVeil.textContent = '';
            return;
        }

        const b = data.bevestiging;
        elTitel.textContent = b.titel;
        if (b.commando) { elCmd.textContent = '$ ' + b.commando; elCmd.classList.remove('hidden'); }
        else { elCmd.classList.add('hidden'); }
        elUitleg.textContent = b.uitleg;
        elVeil.textContent = b.veiligheidstekst || '';
        elVeil.classList.toggle('blok', !b.mag_uitvoeren && !!b.veiligheidstekst);

        if (b.mag_uitvoeren && b.commando) {
            btnRun.textContent = b.extra_waarschuwing ? 'Toch uitvoeren in sandbox' : 'Uitvoeren in sandbox';
            btnRun.classList.remove('hidden');
        } else {
            elHint.textContent = b.commando ? '' : 'Geen uitvoerbaar commando.';
        }
    }

    async function uitvoeren() {
        setStatus('uitvoeren in sandbox…', true);
        btnRun.disabled = true;
        let data;
        try { data = await api('/api/uitvoeren', huidigeInvoer); }
        catch { setStatus('fout', false); btnRun.disabled = false; return; }
        setStatus('klaar', false);
        btnRun.disabled = false;

        const r = data.resultaat || {};
        elRes.textContent = (r.gelukt ? '✓ ' : '✗ ') + (r.uitvoer || '');
        elRes.classList.remove('hidden');
    }

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const tekst = invoer.value.trim();
        if (tekst) vertaal(tekst);
    });

    btnRun.addEventListener('click', uitvoeren);

    document.querySelectorAll('.chip').forEach((chip) => {
        chip.addEventListener('click', () => {
            invoer.value = chip.textContent;
            vertaal(chip.textContent);
        });
    });
})();
