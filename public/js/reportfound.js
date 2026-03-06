document.querySelectorAll(".action-btn").forEach(btn => {
    btn.addEventListener("click", e => {
        e.preventDefault();

        const icon = btn.querySelector("img");
        if (!icon) {
            window.location.href = btn.href;
            return;
        }

        const rect = icon.getBoundingClientRect();
        const clone = icon.cloneNode(true);

        clone.classList.add("icon-fly");
        clone.style.left = rect.left + "px";
        clone.style.top = rect.top + "px";
        clone.style.width = rect.width + "px";
        clone.style.height = rect.height + "px";

        document.body.appendChild(clone);

        // FORCE REFLOW
        clone.getBoundingClientRect();

        /* ===== TARGET POSISI ===== */
        const x =
            window.innerWidth / 2
            - rect.left
            - rect.width / 2
            - 20;

        const y =
            window.innerHeight / 2
            - rect.top
            - rect.height / 2
            + 20;

        /* ===== STEP 1 : ZOOM HALUS ===== */
        requestAnimationFrame(() => {
            clone.style.transform = `
                translate(${x}px, ${y}px)
                scale(3.2)
            `;
        });

        /* ===== STEP 2 : PECAH ===== */
        setTimeout(() => {
            clone.style.opacity = "0";
            clone.style.transform += " scale(1.1)";

            for (let i = 0; i < 14; i++) {
                const p = document.createElement("div");
                p.className = "particle";

                p.style.left = rect.left + rect.width / 2 + "px";
                p.style.top = rect.top + rect.height / 2 + "px";

                const angle = Math.random() * Math.PI * 2;
                const distance = Math.random() * 160 + 80;

                p.style.setProperty("--x", `${Math.cos(angle) * distance}px`);
                p.style.setProperty("--y", `${Math.sin(angle) * distance}px`);

                document.body.appendChild(p);
                setTimeout(() => p.remove(), 800);
            }
        }, 500);

        /* ===== STEP 3 : PINDAH HALAMAN ===== */
        setTimeout(() => {
            window.location.href = btn.href;
        }, 900);
    });
});
