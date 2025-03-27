document.addEventListener("DOMContentLoaded", function () {
    const faqItems = document.querySelectorAll(".faq-container .faq-item");

    faqItems.forEach((item) => {
        const question = item.querySelector(".faq-question");
        const answer = item.querySelector(".faq-answer");
        const toggleSymbol = item.querySelector(".faq-toggle");

        question.addEventListener("click", () => {
            const isVisible = answer.style.display === "block";

            document.querySelectorAll(".faq-container .faq-answer").forEach((ans) => {
                ans.style.display = "none";
            });

            document.querySelectorAll(".faq-container .faq-toggle").forEach((symbol) => {
                symbol.textContent = "+";
            });

            answer.style.display = isVisible ? "none" : "block";
            toggleSymbol.textContent = isVisible ? "+" : "-";
        });
    });
});
