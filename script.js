/* ==========================================================
                    TRASH HUNTERS
                    SCRIPT.JS
========================================================== */

"use strict";

/* ==========================================================
                    ELEMENTOS
========================================================== */

const likeButtons = document.querySelectorAll(".like-btn");
const commentButtons = document.querySelectorAll(".comment-btn");
const comments = document.querySelectorAll(".comments");

const backToTop = document.getElementById("backToTop");
const floatingButton = document.getElementById("floating-post-btn");

/* ==========================================================
                    LIKE (persistido no banco via post/curtir.php)
========================================================== */

likeButtons.forEach(button => {

    button.addEventListener("click", async () => {

        const postId = button.dataset.postId;

        if (!postId) return;

        const icon = button.querySelector("i");
        const counter = button.querySelector("span");

        try {

            const response = await fetch("post/curtir.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "post_id=" + encodeURIComponent(postId)
            });

            const data = await response.json();

            if (data.erro) {
                alert(data.erro);
                return;
            }

            counter.textContent = data.total;

            if (data.curtiu) {
                button.classList.add("liked");
                icon.classList.remove("fa-regular");
                icon.classList.add("fa-solid");
            } else {
                button.classList.remove("liked");
                icon.classList.remove("fa-solid");
                icon.classList.add("fa-regular");
            }

        } catch (e) {
            console.error("Erro ao curtir:", e);
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
    const postId = inputArea.dataset.postId;

    button.addEventListener("click", async () => {

        const text = input.value.trim();

        if (text === "") {
            alert("Digite um comentário.");
            return;
        }

        if (!postId) return;

        try {

            const response = await fetch("post/comentar.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "post_id=" + encodeURIComponent(postId) + "&conteudo=" + encodeURIComponent(text)
            });

            const data = await response.json();

            if (data.erro) {
                alert(data.erro);
                return;
            }

            const avatarSrc = data.avatar ? data.avatar : ("https://ui-avatars.com/api/?background=1f6f4a&color=fff&name=" + encodeURIComponent(data.nome));

            const newComment = document.createElement("div");
            newComment.className = "comment";
            newComment.innerHTML = `
                <img src="${avatarSrc}" alt="${data.nome}">
                <div>
                    <strong>${data.nome}</strong>
                    <p>${data.conteudo}</p>
                </div>
            `;

            inputArea.parentNode.insertBefore(newComment, inputArea);
            input.value = "";

            // Atualiza o contador de comentários no botão daquele post
            const commentBtn = document.querySelector('.comment-btn[data-post-id="' + postId + '"]');
            if (commentBtn) {
                const span = commentBtn.querySelector("span");
                span.textContent = parseInt(span.textContent) + 1;
            }

        } catch (e) {
            console.error("Erro ao comentar:", e);
        }

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

const bellButton = document.getElementById("botao-notificacoes");

const notificationModal = document.getElementById("notificationModal");

if (bellButton) {

    bellButton.addEventListener("click", async () => {

        notificationModal.classList.add("active");

        // Marca como lidas (curtida/comentário) e some com o badge vermelho
        const badge = bellButton.querySelector(".icon-badge");

        if (badge) {

            try {
                await fetch("notificacoes/marcar_lidas.php", { method: "POST" });
                badge.remove();

                document.querySelectorAll(".notification-item.nao-lida").forEach(item => {
                    if (!item.dataset.tipo || item.dataset.tipo !== "mensagem") {
                        item.classList.remove("nao-lida");
                    }
                });

            } catch (e) {
                console.error("Erro ao marcar notificações como lidas:", e);
            }

        }

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

// A publicação agora é feita por um <form> real (post/salvar_postagem.php),
// então não precisamos mais criar o post "na mão" aqui em JS.
// Deixamos só uma validação simples (texto OU mídia) antes de enviar.

const publishButton = document.querySelector(".publish");
const postTextarea = document.querySelector(".new-post textarea");
const midiaInput = document.getElementById("midia-input");

if (publishButton && postTextarea) {

    publishButton.closest("form")?.addEventListener("submit", (event) => {

        const temTexto = postTextarea.value.trim() !== "";
        const temMidia = midiaInput && midiaInput.files.length > 0;

        if (!temTexto && !temMidia) {
            event.preventDefault();
            alert("Escreva algo ou selecione uma foto/vídeo antes de publicar.");
        }

    });

}

/* ==========================================================
                UPLOAD DE MÍDIA NA NOVA POSTAGEM
========================================================== */

const midiaBotao = document.getElementById("midia-botao");
const midiaPreview = document.getElementById("midia-preview");
const midiaPreviewImagem = document.getElementById("midia-preview-imagem");
const midiaPreviewVideo = document.getElementById("midia-preview-video");
const midiaRemover = document.getElementById("midia-remover");

if (midiaBotao && midiaInput) {

    midiaBotao.addEventListener("click", () => {
        midiaInput.click();
    });

    midiaInput.addEventListener("change", () => {

        const arquivo = midiaInput.files[0];

        if (!arquivo) return;

        const tamanhoMaximo = arquivo.type.startsWith("video/") ? 25 * 1024 * 1024 : 5 * 1024 * 1024;

        if (arquivo.size > tamanhoMaximo) {
            alert("Arquivo muito grande. O limite é " + (tamanhoMaximo / 1024 / 1024) + "MB.");
            midiaInput.value = "";
            return;
        }

        const url = URL.createObjectURL(arquivo);

        if (arquivo.type.startsWith("video/")) {
            midiaPreviewVideo.src = url;
            midiaPreviewVideo.style.display = "block";
            midiaPreviewImagem.style.display = "none";
        } else {
            midiaPreviewImagem.src = url;
            midiaPreviewImagem.style.display = "block";
            midiaPreviewVideo.style.display = "none";
        }

        midiaPreview.style.display = "block";

    });

}

if (midiaRemover) {

    midiaRemover.addEventListener("click", () => {

        midiaInput.value = "";
        midiaPreview.style.display = "none";
        midiaPreviewImagem.src = "";
        midiaPreviewVideo.src = "";

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