let pull = document.querySelector("#pull");
let sidebar = document.querySelector(".sidebar");
let link = document.querySelector(".sidebar ul li a");
pull.onclick = () => {
    sidebar.classList.toggle("on");
    window.addEventListener("click", (e) => {
        if (!e.target.matches(".sidebar") && !e.target.matches("#pull")) {
            sidebar.classList.remove("on");
        }
    });
}
