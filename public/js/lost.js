document.querySelectorAll(".report-btn").forEach(btn => {
    btn.addEventListener("click", e => {
        e.preventDefault();

        const icon = btn.querySelector("img");
        const rect = icon.getBoundingClientRect();

        const clone = icon.cloneNode(true);
        clone.style.position = "fixed";
        clone.style.left = rect.left + "px";
        clone.style.top = rect.top + "px";
        clone.style.width = rect.width + "px";
        clone.style.height = rect.height + "px";
        clone.style.zIndex = 9999;

        document.body.appendChild(clone);
        clone.getBoundingClientRect();

        const x = window.innerWidth / 2 - rect.left - rect.width / 2;
        const y = window.innerHeight / 2 - rect.top - rect.height / 2 - 80;

        clone.style.transition =
            "transform .65s cubic-bezier(.22,.61,.36,1), opacity .55s ease";
        clone.style.transform = `translate(${x}px,${y}px) scale(7.5)`;
        clone.style.opacity = 0;

        for (let i = 0; i < 18; i++) {
            const p = document.createElement("div");
            p.className = "particle";
            p.style.left = rect.left + rect.width/2 + "px";
            p.style.top = rect.top + rect.height/2 + "px";

            const a = Math.random() * Math.PI * 2;
            const d = Math.random() * 200 + 80;
            p.style.setProperty("--x", Math.cos(a)*d + "px");
            p.style.setProperty("--y", Math.sin(a)*d + "px");

            document.body.appendChild(p);
            setTimeout(() => p.remove(), 800);
        }

        setTimeout(() => {
            window.location.href = btn.href;
        }, 600);
    });
});
