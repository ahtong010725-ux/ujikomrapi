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
        clone.getBoundingClientRect(); // force reflow

        /* =============================
           POSISI TARGET (DI SINI NGATUR!)
        ============================== */

        const centerX =
            window.innerWidth / 2
            - rect.left
            - rect.width / 2
            - 40;   // ⬅️ tambah minus = geser ke kiri

        const centerY =
            window.innerHeight / 2
            - rect.top
            - rect.height / 2
            + 20;   // ⬇️ plus = turun ke bawah

        /* ===== STEP 1 : ZOOM KE TENGAH ===== */
        clone.style.transition =
            "transform .75s cubic-bezier(.16,1,.3,1), opacity .6s ease";

        clone.style.transform = `
            translate(${centerX}px, ${centerY}px)
            scale(8.5)
        `;

        /* ===== STEP 2 : FADE + PECAH ===== */
        setTimeout(() => {
            clone.style.opacity = "0";
            clone.style.transform += " scale(1.15)";

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
                setTimeout(() => p.remove(), 900);
            }
        }, 520);

        /* ===== STEP 3 : PINDAH HALAMAN ===== */
        setTimeout(() => {
            window.location.href = btn.href;
        }, 900);
    });
});
function toggleDropdown() {
    const menu = document.getElementById("dropdownMenu");
    menu.style.display = menu.style.display === "flex" ? "none" : "flex";
}

window.addEventListener("click", function(e) {
    const menu = document.getElementById("dropdownMenu");
    const avatar = document.querySelector(".user-avatar");

    if (!avatar.contains(e.target)) {
        menu.style.display = "none";
    }
});
