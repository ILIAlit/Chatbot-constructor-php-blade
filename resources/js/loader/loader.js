export function loadingTrue() {
    const mainScreen = document.getElementById("main-screen");
    const loader = document.getElementById("loader");
    mainScreen.classList.add("page-locked");
    loader.classList.remove("d-none");
}

export function loadingFalse() {
    const mainScreen = document.getElementById("main-screen");
    const loader = document.getElementById("loader");
    mainScreen.classList.remove("page-locked");
    loader.classList.add("d-none");
}
