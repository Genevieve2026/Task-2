let iconCart = document.querySelector(".icon-cart");
let body = document.querySelector("body");
let closeCart = document.querySelector(".close");

iconCart.addEventListener("click",() => {
    body.classList.toggle("showCart");
});
closeCart.addEventListener("click",() => {
    body.classList.toggle("showCart");
});