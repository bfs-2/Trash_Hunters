/* ==========================================================
                    TRASH HUNTERS
                    SCRIPT.JS
========================================================== */

"use strict";

/* ==========================================================
                    ELEMENTOS
========================================================== */

const likeButtons = document.querySelectorAll(".like-btn");
const dislikeButtons = document.querySelectorAll(".dislike-btn");
const commentButtons = document.querySelectorAll(".comment-btn");
const comments = document.querySelectorAll(".comments");

const backToTop = document.getElementById("backToTop");
const floatingButton = document.getElementById("floating-post-btn");

/* ==========================================================
                    LIKE
========================================================== */

likeButtons.forEach(button => {

    button.addEventListener("click", () => {

        const icon = button.querySelector("i");
        const counter = button.querySelector("span");

        let likes = parseInt(counter.textContent);

        if (!button.classList.contains("liked")) {

            button.classList.add("liked");

            icon.classList.remove("fa-regular");
            icon.classList.add("fa-solid");

            counter.textContent = likes + 1;

        } else {

            button.classList.remove("liked");

            icon.classList.remove("fa-solid");
            icon.classList.add("fa-regular");

            counter.textContent = likes - 1;

        }

    });

});

/* ==========================================================
                    DESLIKE
========================================================== */

dislikeButtons.forEach(button => {

    button.addEventListener("click", () => {

        const icon = button.querySelector("i");
        const counter = button.querySelector("span");

        let dislikes = parseInt(counter.textContent);

        if (!button.classList.contains("disliked")) {

            button.classList.add("disliked");

            icon.classList.remove("fa-regular");
            icon.classList.add("fa-solid");

            counter.textContent = dislikes + 1;

        } else {

            button.classList.remove("disliked");

            icon.classList.remove("fa-solid");
            icon.classList.add("fa-regular");

            counter.textContent = dislikes - 1;

        }

    });

});

/* ==========================================================
                    COMENTÁRIOS
========================================================== */

comments.forEach(comment => {

    comment.style.display = "none";

});

commentButtons.forEach((button, index) => {

    button.addEventListener("click", () => {

        if (comments[index].style.display === "none") {

            comments[index].style.display = "block";

        } else {

            comments[index].style.display = "none";

        }

    });

});

/* ==========================================================
                BOTÃO VOLTAR AO TOPO
========================================================== */

window.addEventListener("scroll", () => {

    if (window.scrollY > 400) {

        backToTop.classList.add("show");

    } else {

        backToTop.classList.remove("show");

    }

});

backToTop.addEventListener("click", () => {

    window.scrollTo({

        top: 0,

        behavior: "smooth"

    });

});

/* ==========================================================
                BOTÃO NOVA POSTAGEM
========================================================== */

floatingButton.addEventListener("click", () => {

    const textarea = document.querySelector(".new-post textarea");

    if (textarea) {

        textarea.scrollIntoView({

            behavior: "smooth",

            block: "center"

        });

        textarea.focus();

    }

});

/* ==========================================================
                ANIMAÇÃO DOS POSTS
========================================================== */

const posts = document.querySelectorAll(".post");

const observer = new IntersectionObserver((entries) => {

    entries.forEach(entry => {

        if (entry.isIntersecting) {

            entry.target.style.opacity = "1";
            entry.target.style.transform = "translateY(0)";

        }

    });

}, {

    threshold: 0.15

});

posts.forEach(post => {

    post.style.opacity = "0";
    post.style.transform = "translateY(40px)";
    post.style.transition = ".6s";

    observer.observe(post);

});

/* ==========================================================
                    MENSAGEM
========================================================== */

console.log("Trash Hunters iniciado com sucesso! 🌿♻");

/* ==========================================================
                ENVIAR COMENTÁRIOS
========================================================== */

const commentInputs = document.querySelectorAll(".comment-input");

commentInputs.forEach(inputArea => {

    const input = inputArea.querySelector("input");
    const button = inputArea.querySelector("button");

    button.addEventListener("click", () => {

        const text = input.value.trim();

        if(text === ""){

            alert("Digite um comentário.");

            return;

        }

        const newComment = document.createElement("div");

        newComment.className = "comment";

        newComment.innerHTML = `

            <img src="assets/avatars/avatar.png" alt="Avatar">

            <div>

                <strong>Você</strong>

                <p>${text}</p>

            </div>

        `;

        inputArea.parentNode.insertBefore(newComment, inputArea);

        input.value = "";

    });

});


/* ==========================================================
                    COMPARTILHAR
========================================================== */

const shareButtons = document.querySelectorAll(".share-btn");
const shareModal = document.getElementById("shareModal");

shareButtons.forEach(button=>{

    button.addEventListener("click",()=>{

        shareModal.classList.add("active");

    });

});


/* ==========================================================
                FECHAR MODAL
========================================================== */

const closeButtons = document.querySelectorAll(".close-modal");

closeButtons.forEach(button=>{

    button.addEventListener("click",()=>{

        document.querySelectorAll(".modal").forEach(modal=>{

            modal.classList.remove("active");

        });

    });

});


/* ==========================================================
            FECHAR CLICANDO FORA
========================================================== */

document.querySelectorAll(".modal").forEach(modal=>{

    modal.addEventListener("click",(e)=>{

        if(e.target===modal){

            modal.classList.remove("active");

        }

    });

});


/* ==========================================================
                COPIAR LINK
========================================================== */

const copyButton = document.querySelector(".share-options button:last-child");

if(copyButton){

copyButton.addEventListener("click",()=>{

navigator.clipboard.writeText(window.location.href);

alert("Link copiado!");

});

}


/* ==========================================================
            WEB SHARE API (CELULAR)
========================================================== */

const shareOptions = document.querySelectorAll(".share-options button");

shareOptions.forEach(button=>{

button.addEventListener("click",async()=>{

if(button.innerText.includes("Copiar")) return;

if(navigator.share){

try{

await navigator.share({

title:"Trash Hunters",

text:"Confira esta publicação!",

url:window.location.href

});

}catch(e){

console.log(e);

}

}

});

});


/* ==========================================================
                VISUALIZADOR DE IMAGEM
========================================================== */

const previewModal=document.querySelector(".image-modal");

const previewImage=document.getElementById("previewImage");

const images=document.querySelectorAll(".post-image, .gallery img");

images.forEach(image=>{

image.addEventListener("click",()=>{

previewImage.src=image.src;

previewModal.classList.add("active");

});

});


/* ==========================================================
            FECHAR VISUALIZADOR
========================================================== */

const closeImage=document.querySelector(".close-image");

if(closeImage){

closeImage.addEventListener("click",()=>{

previewModal.classList.remove("active");

});

}

previewModal.addEventListener("click",(e)=>{

if(e.target===previewModal){

previewModal.classList.remove("active");

}

});


/* ==========================================================
            ABRIR NOTIFICAÇÕES
========================================================== */

const bellButton=document.querySelector(".top-icons button");

const notificationModal=document.getElementById("notificationModal");

if(bellButton){

bellButton.addEventListener("click",()=>{

notificationModal.classList.add("active");

});

}


/* ==========================================================
            EFEITO NOS BOTÕES
========================================================== */

document.querySelectorAll("button").forEach(button=>{

button.addEventListener("mousedown",()=>{

button.style.transform="scale(.95)";

});

button.addEventListener("mouseup",()=>{

button.style.transform="";

});

button.addEventListener("mouseleave",()=>{

button.style.transform="";

});

});


/* ==========================================================
                FIM DA PARTE 2
========================================================== */

/* ==========================================================
                    PESQUISA
========================================================== */

const searchInput = document.querySelector(".search input");

if (searchInput) {

    searchInput.addEventListener("keyup", function () {

        const text = this.value.toLowerCase();

        const posts = document.querySelectorAll(".post");

        posts.forEach(post => {

            if (post.innerText.toLowerCase().includes(text)) {

                post.style.display = "block";

            } else {

                post.style.display = "none";

            }

        });

    });

}

/* ==========================================================
                NOVA POSTAGEM
========================================================== */

const publishButton = document.querySelector(".publish");
const postTextarea = document.querySelector(".new-post textarea");

if (publishButton && postTextarea) {

    publishButton.addEventListener("click", () => {

        const text = postTextarea.value.trim();

        if (text === "") {

            alert("Escreva algo antes de publicar.");

            return;

        }

        const feed = document.querySelector(".feed");

        const article = document.createElement("article");

        article.className = "post";

        article.innerHTML = `

        <div class="post-top">

            <img src="assets/avatars/avatar.png">

            <div>

                <h3>Você</h3>

                <span>Agora mesmo</span>

            </div>

        </div>

        <p>${text}</p>

        <div class="post-footer">

            <button class="like-btn">

                <i class="fa-regular fa-heart"></i>

                <span>0</span>

            </button>

            <button class="dislike-btn">

                <i class="fa-regular fa-thumbs-down"></i>

                <span>0</span>

            </button>

            <button class="comment-btn">

                <i class="fa-regular fa-comment"></i>

                <span>0</span>

            </button>

            <button class="share-btn">

                <i class="fa-solid fa-share"></i>

                Compartilhar

            </button>

        </div>

        <section class="comments" style="display:none;">

            <div class="comment-input">

                <input type="text" placeholder="Escreva um comentário...">

                <button>Enviar</button>

            </div>

        </section>

        `;

        feed.insertBefore(article, feed.children[1]);

        postTextarea.value = "";

        alert("Postagem publicada com sucesso!");

    });

}

/* ==========================================================
                ENTER NO COMENTÁRIO
========================================================== */

document.addEventListener("keydown", function (event) {

    if (event.key === "Enter") {

        const active = document.activeElement;

        if (

            active &&
            active.closest(".comment-input")

        ) {

            event.preventDefault();

            active.nextElementSibling.click();

        }

    }

});

/* ==========================================================
                TEMPO DAS POSTAGENS
========================================================== */

setInterval(() => {

    document.querySelectorAll(".post-top span").forEach(span => {

        if (span.textContent === "Agora mesmo") {

            span.textContent = "há poucos segundos";

        }

    });

}, 60000);

/* ==========================================================
                ANIMAÇÃO DE ENTRADA
========================================================== */

window.addEventListener("load", () => {

    document.body.style.opacity = "1";

});

/* ==========================================================
                CONSOLE
========================================================== */

console.log("===================================");

console.log("🌿 Trash Hunters");

console.log("Rede Social carregada com sucesso!");

console.log("===================================");

/* ==========================================================
                INICIALIZAÇÃO
========================================================== */

document.addEventListener("DOMContentLoaded", () => {

    console.log("Sistema iniciado.");

});