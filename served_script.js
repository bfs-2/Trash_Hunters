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
const postsList = document.getElementById("postsList");

function toggleChatPanel() {
    const toggleButton = document.getElementById('toggle-chat');
    const chatBody = document.getElementById('chat-body');
    if (!toggleButton || !chatBody) return;

    const currentDisplay = window.getComputedStyle(chatBody).display;
    const isHidden = currentDisplay === 'none';

    chatBody.style.display = isHidden ? 'block' : 'none';
    toggleButton.setAttribute('aria-expanded', String(isHidden));
    toggleButton.innerHTML = isHidden
        ? '<i class="fa-solid fa-minus"></i>'
        : '<i class="fa-solid fa-plus"></i>';
}

window.toggleChat = function(event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
        if (typeof event.stopImmediatePropagation === 'function') {
            event.stopImmediatePropagation();
        }
        event.returnValue = false;
    }

    toggleChatPanel();
    return false;
};

function setChatOpen(isOpen) {
    const toggleButton = document.getElementById('toggle-chat');
    const chatBody = document.getElementById('chat-body');
    if (!toggleButton || !chatBody) return;

    chatBody.style.display = isOpen ? 'block' : 'none';
    toggleButton.setAttribute('aria-expanded', String(isOpen));
    toggleButton.innerHTML = isOpen
        ? '<i class="fa-solid fa-minus"></i>'
        : '<i class="fa-solid fa-plus"></i>';
}

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
                    COMENTÃRIOS
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
                BOTÃO VOLTAR AO TOPO
========================================================== */

if (backToTop) {

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

}

/* ==========================================================
                BOTÃO NOVA POSTAGEM
========================================================== */

if (floatingButton) {

    floatingButton.addEventListener("click", (event) => {

        event.preventDefault();
        event.stopPropagation();

        const chatBody = document.getElementById('chat-body');

        if (!chatBody) return;

        toggleChatPanel();

    });

}

/* ==========================================================
                ANIMAÃÃO DOS POSTS
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

console.log("Trash Hunters iniciado com sucesso! ð¿â»");

/* ==========================================================
                ENVIAR COMENTÃRIOS
========================================================== */

const commentInputs = document.querySelectorAll(".comment-input");

commentInputs.forEach(inputArea => {

    const input = inputArea.querySelector("input");
    const button = inputArea.querySelector("button");
    const postId = inputArea.dataset.postId;

    button.addEventListener("click", async () => {

        const text = input.value.trim();

        if (text === "") {
            alert("Digite um comentÃ¡rio.");
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

            // Atualiza o contador de comentÃ¡rios no botÃ£o daquele post
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

text:"Confira esta publicaÃ§Ã£o!",

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
            ABRIR NOTIFICAÃÃES
========================================================== */

const bellButton = document.getElementById("botao-notificacoes");

const notificationModal = document.getElementById("notificationModal");

if (bellButton) {

    bellButton.addEventListener("click", async () => {

        notificationModal.classList.add("active");

        // Marca como lidas (curtida/comentÃ¡rio) e some com o badge vermelho
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
                console.error("Erro ao marcar notificaÃ§Ãµes como lidas:", e);
            }

        }

    });

}

const coletaNav = document.getElementById("nav-explorar");
const coletaModal = document.getElementById("coletaModal");

if (coletaNav && coletaModal) {
    coletaNav.addEventListener("click", (event) => {
        event.preventDefault();
        coletaModal.classList.add("active");
    });
}


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

// A publicaÃ§Ã£o agora Ã© feita por um <form> real (post/salvar_postagem.php),
// entÃ£o nÃ£o precisamos mais criar o post "na mÃ£o" aqui em JS.
// Deixamos sÃ³ uma validaÃ§Ã£o simples (texto OU mÃ­dia) antes de enviar.

const publishButton = document.querySelector(".publish");
const postTextarea = document.querySelector(".new-post textarea");
const midiaInput = document.getElementById("midia-input");

if (publishButton && postTextarea) {

    publishButton.closest("form")?.addEventListener("submit", (event) => {

        const temTexto = postTextarea.value.trim() !== "";
        const temMidia = midiaInput && midiaInput.files.length > 0;

        if (!temTexto && !temMidia) {
            event.preventDefault();
            alert("Escreva algo ou selecione uma foto/vÃ­deo antes de publicar.");
        }

    });

}

/* ==========================================================
                UPLOAD DE MÃDIA NA NOVA POSTAGEM
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
            alert("Arquivo muito grande. O limite Ã© " + (tamanhoMaximo / 1024 / 1024) + "MB.");
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
                ENTER NO COMENTÃRIO
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

function formatRelativeTime(dateString) {
    const now = new Date();
    const created = new Date(dateString.replace(' ', 'T'));
    if (isNaN(created)) return 'hÃ¡ pouco';
    const diffMs = now - created;
    const diffSec = Math.floor(diffMs / 1000);
    const diffMin = Math.floor(diffSec / 60);
    const diffH = Math.floor(diffMin / 60);
    const diffD = Math.floor(diffH / 24);
    const diffM = Math.floor(diffD / 30);
    const diffY = Math.floor(diffD / 365);

    if (diffSec < 10) return 'agora mesmo';
    if (diffSec < 60) return 'hÃ¡ poucos segundos';
    if (diffMin < 60) return `hÃ¡ ${diffMin} ${diffMin === 1 ? 'minuto' : 'minutos'}`;
    if (diffH < 24) return `hÃ¡ ${diffH} ${diffH === 1 ? 'hora' : 'horas'}`;
    if (diffD < 30) return `hÃ¡ ${diffD} ${diffD === 1 ? 'dia' : 'dias'}`;
    if (diffM < 12) return `hÃ¡ ${diffM} ${diffM === 1 ? 'mÃªs' : 'meses'}`;
    return `hÃ¡ ${diffY} ${diffY === 1 ? 'ano' : 'anos'}`;
}

function refreshPostTimestamps() {
    document.querySelectorAll('.post-top span[data-created]').forEach(span => {
        const createdAt = span.dataset.created;
        if (!createdAt) return;
        span.textContent = formatRelativeTime(createdAt);
    });
}

refreshPostTimestamps();
setInterval(refreshPostTimestamps, 60000);

/* ==========================================================
                ANIMAÃÃO DE ENTRADA
========================================================== */

window.addEventListener("load", () => {

    document.body.style.opacity = "1";

});

/* ==========================================================
                CONSOLE
========================================================== */

console.log("===================================");

console.log("ð¿ Trash Hunters");

console.log("Rede Social carregada com sucesso!");

console.log("===================================");

function escapeHtml(text) {
    if (text === null || text === undefined) {
        return '';
    }
    text = String(text);
    return text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function nl2br(text) {
    return escapeHtml(text).replace(/\n/g, "<br>");
}

const mensagensEco = [
    "ð Uma garrafa PET pode levar atÃ© 450 anos para se decompor.",
    "â»ï¸ Reciclar uma lata de alumÃ­nio economiza atÃ© 95% da energia necessÃ¡ria para produzir uma nova.",
    "ð§ Fechar a torneira ao escovar os dentes pode economizar atÃ© 12 litros de Ã¡gua.",
    "ð³ Uma Ã¡rvore adulta pode absorver cerca de 22 kg de COâ por ano.",
    "ð Pilhas e baterias nunca devem ser descartadas no lixo comum.",
    "ðï¸ Utilizar sacolas reutilizÃ¡veis reduz significativamente o consumo de plÃ¡stico.",
    "ð² Trocar o carro pela bicicleta em pequenos trajetos reduz a emissÃ£o de poluentes.",
    "ð Reciclar uma tonelada de papel pode salvar atÃ© 20 Ã¡rvores.",
    "ð O plÃ¡stico descartado incorretamente pode chegar aos oceanos e afetar a vida marinha.",
    "ð Compostagem transforma resÃ­duos orgÃ¢nicos em adubo natural.",
    "ð¡ LÃ¢mpadas LED consomem menos energia e possuem maior durabilidade.",
    "ð¿ Reduzir o tempo do banho ajuda a economizar Ã¡gua e energia.",
    "ð± Plantar Ã¡rvores ajuda a combater o aquecimento global.",
    "ð¥¤ Canudos descartÃ¡veis podem ser substituÃ­dos por opÃ§Ãµes reutilizÃ¡veis.",
    "ðï¸ Separar corretamente os resÃ­duos facilita a reciclagem.",
    "ð± Celulares antigos devem ser descartados em pontos de coleta eletrÃ´nica.",
    "ð A energia solar Ã© uma das fontes de energia mais limpas disponÃ­veis.",
    "ð¯ Jogar lixo na rua contribui para enchentes e poluiÃ§Ã£o urbana.",
    "ð¢ Animais marinhos frequentemente confundem plÃ¡stico com alimento.",
    "ðï¸ Preservar Ã¡reas verdes melhora a qualidade do ar nas cidades.",
    "â»ï¸ O vidro pode ser reciclado infinitas vezes sem perder qualidade.",
    "ð§´ Embalagens de produtos de limpeza tambÃ©m podem ser recicladas.",
    "ð° Uma torneira pingando pode desperdiÃ§ar dezenas de litros de Ã¡gua por dia.",
    "ð Pequenas atitudes sustentÃ¡veis fazem grande diferenÃ§a para o planeta.",
    "ð¤ A conscientizaÃ§Ã£o ambiental comeÃ§a com aÃ§Ãµes individuais e se fortalece na comunidade."
];

const fatosHistoricos = [
    "ð 1970 â Foi comemorado o primeiro Dia da Terra, dando inÃ­cio ao movimento ambiental moderno.",
    "ð 1972 â A ConferÃªncia de Estocolmo foi a primeira grande reuniÃ£o mundial sobre meio ambiente.",
    "â»ï¸ 1988 â O sÃ­mbolo internacional da reciclagem se popularizou mundialmente.",
    "ð³ 1992 â O Brasil sediou a ECO-92 no Rio de Janeiro, um dos maiores eventos ambientais da histÃ³ria.",
    "ð 1997 â Foi assinado o Protocolo de Kyoto para reduÃ§Ã£o da emissÃ£o de gases poluentes.",
    "ð± 2005 â O Protocolo de Kyoto entrou oficialmente em vigor.",
    "ð¢ 2015 â 193 paÃ­ses aprovaram os Objetivos de Desenvolvimento SustentÃ¡vel da ONU.",
    "ð¡ï¸ 2015 â O Acordo de Paris foi criado para combater as mudanÃ§as climÃ¡ticas.",
    "â»ï¸ 2017 â O QuÃªnia proibiu sacolas plÃ¡sticas, tornando-se referÃªncia mundial.",
    "ð 1986 â Entrou em vigor a moratÃ³ria internacional da caÃ§a comercial Ã s baleias.",
    "ð³ 1962 â O livro 'Primavera Silenciosa' alertou o mundo sobre os impactos dos pesticidas.",
    "ð 1987 â O Protocolo de Montreal iniciou o combate aos gases que destruÃ­am a camada de ozÃ´nio.",
    "âï¸ 2016 â A energia solar tornou-se a fonte energÃ©tica que mais cresceu no mundo.",
    "ð² 2019 â MilhÃµes de pessoas participaram das greves globais pelo clima.",
    "ð 2022 â O Brasil ultrapassou 20 GW de capacidade instalada em energia solar."
];

let ecoIndex = 0;
let historyIndex = 0;

function rotateEcoMessage() {
    const ecoEl = document.getElementById('ecoMessage');
    if (!ecoEl) return;
    ecoEl.textContent = mensagensEco[ecoIndex];
    ecoIndex = (ecoIndex + 1) % mensagensEco.length;
}

function rotateHistoryMessage() {
    const historyEl = document.getElementById('historyMessage');
    if (!historyEl) return;
    historyEl.textContent = fatosHistoricos[historyIndex];
    historyIndex = (historyIndex + 1) % fatosHistoricos.length;
}

function getLatestPostId() {
    const firstPost = postsList?.querySelector('.post');
    if (!firstPost) return 0;
    return Number(firstPost.dataset.postId) || 0;
}

function removeEmptyState() {
    const empty = postsList?.querySelector('.empty-state');
    if (empty) {
        empty.remove();
    }
}

function createPostElement(post) {
    const article = document.createElement('article');
    article.className = 'post';
    article.id = 'post-' + post.id;
    article.dataset.postId = post.id;

    const likedClass = post.eu_curti ? ' liked' : '';
    const heartIcon = post.eu_curti ? 'solid' : 'regular';

    const authorMenu = `
            <div class="post-options">
                <button class="post-menu-trigger" type="button" aria-label="Abrir opÃ§Ãµes da postagem">
                    <i class="fa-solid fa-ellipsis-vertical"></i>
                </button>
                <div class="post-menu">
                    ${post.is_author ? `<button type="button" class="delete-post-btn" data-post-id="${escapeHtml(post.id)}">Excluir publicaÃ§Ã£o</button>` : ''}
                    <button type="button" class="save-post-btn" data-post-id="${escapeHtml(post.id)}" data-salvo="${post.is_saved ? '1' : '0'}">
                        ${post.is_saved ? 'Remover dos salvos' : 'Salvar publicaÃ§Ã£o'}
                    </button>
                </div>
            </div>
        `;

    article.innerHTML = `
        <div class="post-top">
            <img src="${escapeHtml(post.autor_avatar)}" alt="${escapeHtml(post.autor_nome)}">
            <div>
                <h3>${escapeHtml(post.autor_nome)}</h3>
                <span data-created="${escapeHtml(post.created_at)}">${escapeHtml(post.data_relativa)}</span>
            </div>
            ${authorMenu}
        </div>
        <p>${nl2br(post.conteudo)}</p>
        ${(() => {
            if (Array.isArray(post.midias) && post.midias.length) {
                const count = post.midias.length;
                const items = post.midias.map((m, idx) => {
                    if (m.tipo === 'video') return `<div class="gallery-item" data-index="${idx}" data-type="video"><video class="gallery-media" src="${escapeHtml(m.caminho)}" preload="metadata"></video><div class="video-overlay"><i class="fa-solid fa-play"></i></div></div>`;
                    return `<div class="gallery-item" data-index="${idx}" data-type="imagem"><img class="gallery-media" src="${escapeHtml(m.caminho)}" alt="Imagem da postagem"></div>`;
                }).join('');
                return `<div class="post-gallery" data-count="${count}"><div class="gallery-grid">${items}</div>${count>1?`<div class="gallery-badge">1/${count}</div>`:''}</div>`;
            }
            // fallback for older posts
            if (post.midia) {
                return post.midia_tipo === 'video'
                    ? `<div class="post-gallery"><div class="gallery-grid"><div class="gallery-item" data-type="video" data-index="0"><video class="gallery-media" src="${escapeHtml(post.midia)}" preload="metadata"></video><div class="video-overlay"><i class="fa-solid fa-play"></i></div></div></div></div>`
                    : `<div class="post-gallery"><div class="gallery-grid"><div class="gallery-item" data-type="imagem" data-index="0"><img class="gallery-media" src="${escapeHtml(post.midia)}" alt="Imagem da postagem"></div></div></div>`;
            }
            return '';
        })()}
        <div class="post-footer">
            <button class="like-btn${likedClass}" data-post-id="${post.id}">
                <i class="fa-${heartIcon} fa-heart"></i>
                <span>${post.total_curtidas}</span>
            </button>
            <button class="comment-btn" data-post-id="${post.id}">
                <i class="fa-regular fa-comment"></i>
                <span>${post.comentarios_total}</span>
            </button>
            <button class="share-btn">
                <i class="fa-solid fa-share"></i>
                Compartilhar
            </button>
        </div>
        <section class="comments"></section>
    `;

    const commentsSection = article.querySelector('.comments');
    commentsSection.innerHTML = `
        <div class="comment-input" data-post-id="${post.id}">
            <input type="text" placeholder="Escreva um comentÃ¡rio...">
            <button>Enviar</button>
        </div>
    `;
    commentsSection.style.display = 'none';

    return article;
}

function bindLikeButtons(root) {
    root.querySelectorAll('.like-btn').forEach(button => {
        if (button.dataset.bound === 'true') return;
        button.dataset.bound = 'true';

        button.addEventListener('click', async () => {
            const postId = button.dataset.postId;
            if (!postId) return;

            const icon = button.querySelector('i');
            const counter = button.querySelector('span');

            try {
                const response = await fetch('post/curtir.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'post_id=' + encodeURIComponent(postId)
                });

                const data = await response.json();

                if (data.erro) {
                    alert(data.erro);
                    return;
                }

                counter.textContent = data.total;

                if (data.curtiu) {
                    button.classList.add('liked');
                    icon.classList.remove('fa-regular');
                    icon.classList.add('fa-solid');
                } else {
                    button.classList.remove('liked');
                    icon.classList.remove('fa-solid');
                    icon.classList.add('fa-regular');
                }
            } catch (e) {
                console.error('Erro ao curtir:', e);
            }
        });
    });
}

function bindCommentButtons(root) {
    root.querySelectorAll('.comment-btn').forEach(button => {
        if (button.dataset.bound === 'true') return;
        button.dataset.bound = 'true';

        button.addEventListener('click', () => {
            const commentsSection = button.closest('.post')?.querySelector('.comments');
            if (!commentsSection) return;
            commentsSection.style.display = commentsSection.style.display === 'none' ? 'block' : 'none';
        });
    });
}

function bindCommentInputs(root) {
    root.querySelectorAll('.comment-input').forEach(inputArea => {
        if (inputArea.dataset.bound === 'true') return;
        inputArea.dataset.bound = 'true';

        const input = inputArea.querySelector('input');
        const button = inputArea.querySelector('button');
        const postId = inputArea.dataset.postId;

        button.addEventListener('click', async () => {
            const text = input.value.trim();
            if (text === '') {
                alert('Digite um comentÃ¡rio.');
                return;
            }
            if (!postId) return;

            try {
                const response = await fetch('post/comentar.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'post_id=' + encodeURIComponent(postId) + '&conteudo=' + encodeURIComponent(text)
                });

                const data = await response.json();
                if (data.erro) {
                    alert(data.erro);
                    return;
                }

                const avatarSrc = data.avatar ? data.avatar : ('https://ui-avatars.com/api/?background=1f6f4a&color=fff&name=' + encodeURIComponent(data.nome));
                const newComment = document.createElement('div');
                newComment.className = 'comment';
                newComment.innerHTML = `
                    <img src="${avatarSrc}" alt="${escapeHtml(data.nome)}">
                    <div>
                        <strong>${escapeHtml(data.nome)}</strong>
                        <p>${escapeHtml(data.conteudo)}</p>
                    </div>
                `;

                inputArea.parentNode.insertBefore(newComment, inputArea);
                input.value = '';

                const commentBtn = document.querySelector('.comment-btn[data-post-id="' + postId + '"]');
                if (commentBtn) {
                    const span = commentBtn.querySelector('span');
                    span.textContent = parseInt(span.textContent) + 1;
                }
            } catch (e) {
                console.error('Erro ao comentar:', e);
            }
        });
    });
}

function bindShareButtons(root) {
    root.querySelectorAll('.share-btn').forEach(button => {
        if (button.dataset.bound === 'true') return;
        button.dataset.bound = 'true';

        button.addEventListener('click', () => {
            shareModal.classList.add('active');
        });
    });
}

function bindPostMenus(root) {
    root.querySelectorAll('.post-menu-trigger').forEach(button => {
        if (button.dataset.bound === 'true') return;
        button.dataset.bound = 'true';

        button.addEventListener('click', (event) => {
            event.stopPropagation();
            const options = button.closest('.post-options');
            if (!options) return;
            options.classList.toggle('active');
        });
    });

    root.querySelectorAll('.delete-post-btn').forEach(button => {
        if (button.dataset.bound === 'true') return;
        button.dataset.bound = 'true';

        button.addEventListener('click', async () => {
            const postId = button.dataset.postId;
            if (!postId) return;

            if (!confirm('Deseja realmente excluir esta publicaÃ§Ã£o?')) {
                return;
            }

            try {
                const response = await fetch('post/excluir_post.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'post_id=' + encodeURIComponent(postId)
                });

                const data = await response.json();

                if (data.erro) {
                    alert(data.erro);
                    return;
                }

                const article = document.getElementById('post-' + postId);
                if (article) {
                    article.remove();
                }
            } catch (e) {
                console.error('Erro ao excluir publicaÃ§Ã£o:', e);
                alert('NÃ£o foi possÃ­vel excluir a publicaÃ§Ã£o. Tente novamente.');
            }
        });
    });

    root.querySelectorAll('.save-post-btn').forEach(button => {
        if (button.dataset.bound === 'true') return;
        button.dataset.bound = 'true';

        button.addEventListener('click', async () => {
            const postId = button.dataset.postId;
            if (!postId) return;
            const currentlySaved = button.dataset.salvo === '1';

            try {
                const response = await fetch('post/toggle_salvar.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'post_id=' + encodeURIComponent(postId)
                });
                const data = await response.json();
                if (data.erro) {
                    alert(data.erro);
                    return;
                }
                button.dataset.salvo = data.salvo ? '1' : '0';
                button.textContent = data.salvo ? 'Remover dos salvos' : 'Salvar publicaÃ§Ã£o';
                if (button.closest('.post-options')) {
                    button.closest('.post-options').classList.remove('active');
                }
            } catch (e) {
                console.error('Erro ao salvar publicaÃ§Ã£o:', e);
                alert('NÃ£o foi possÃ­vel salvar a publicaÃ§Ã£o. Tente novamente.');
            }
        });
    });
}

function bindPostEvents(root) {
    bindLikeButtons(root);
    bindCommentButtons(root);
    bindCommentInputs(root);
    bindShareButtons(root);
    bindPostMenus(root);
}

async function fetchNewPosts() {
    if (!postsList) return;

    const afterId = getLatestPostId();
    try {
        const response = await fetch('post/ultimas_postagens.php?after_id=' + afterId);
        if (!response.ok) return;

        const posts = await response.json();
        if (!Array.isArray(posts) || posts.length === 0) return;

        removeEmptyState();
        posts.forEach(post => {
            const postElement = createPostElement(post);
            postsList.insertAdjacentElement('afterbegin', postElement);
            bindPostEvents(postElement);
            observer.observe(postElement);
        });
    } catch (e) {
        console.error('Erro ao buscar novas postagens:', e);
    }
}

if (postsList) {
    fetchNewPosts();
    setInterval(fetchNewPosts, 15000);
}

/* ==========================================================
                INICIALIZAÃÃO
========================================================== */

document.addEventListener("DOMContentLoaded", () => {

    console.log("Sistema iniciado.");

    const toggleButton = document.getElementById('toggle-chat');
    const chatBody = document.getElementById('chat-body');

    if (!toggleButton || !chatBody) return;

    setChatOpen(window.getComputedStyle(chatBody).display !== 'none');

    toggleButton.addEventListener('click', function (event) {
        event.preventDefault();
        event.stopPropagation();
        window.toggleChat(event);
    });

    // Vincula eventos (likes, comentÃ¡rios, menu, excluir) para posts jÃ¡ carregados
    if (postsList) {
        bindPostEvents(postsList);
    }

    rotateEcoMessage();
    rotateHistoryMessage();
    setInterval(rotateEcoMessage, 15000);
    setInterval(rotateHistoryMessage, 15000);
});

console.log("Script carregado!");

/* ================= Lightbox: delegaÃ§Ã£o e controle ================= */
(function(){
    function buildLightbox() {
        if (document.getElementById('mediaLightbox')) return;
        const lb = document.createElement('div');
        lb.id = 'mediaLightbox';
        lb.className = 'media-lightbox';
        lb.style.display = 'none';
        lb.innerHTML = `
            <div class="inner">
                <button class="lightbox-close" aria-label="Fechar">&times;</button>
                <button class="lightbox-prev" aria-label="Anterior">&#10094;</button>
                <button class="lightbox-next" aria-label="PrÃ³ximo">&#10095;</button>
                <div class="lightbox-counter"></div>
                <div class="lightbox-content"></div>
            </div>
        `;
        document.body.appendChild(lb);

        lb.querySelector('.lightbox-close').addEventListener('click', closeLightbox);
        lb.querySelector('.lightbox-prev').addEventListener('click', () => shiftLightbox(-1));
        lb.querySelector('.lightbox-next').addEventListener('click', () => shiftLightbox(1));
        lb.addEventListener('click', (e)=>{ if (e.target === lb) closeLightbox(); });
        document.addEventListener('keydown', (e)=>{
            if (!lb || lb.style.display === 'none') return;
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowLeft') shiftLightbox(-1);
            if (e.key === 'ArrowRight') shiftLightbox(1);
        });
    }

    let LB_ITEMS = [];
    let LB_INDEX = 0;

    function openLightbox(items, startIndex) {
        buildLightbox();
        LB_ITEMS = items;
        LB_INDEX = startIndex || 0;
        renderLightbox();
        const lb = document.getElementById('mediaLightbox');
        lb.style.display = 'flex';
    }

    function closeLightbox(){
        const lb = document.getElementById('mediaLightbox');
        if (!lb) return;
        lb.style.display = 'none';
        lb.querySelector('.lightbox-content').innerHTML = '';
    }

    function shiftLightbox(delta){
        if (!LB_ITEMS.length) return;
        LB_INDEX = (LB_INDEX + delta + LB_ITEMS.length) % LB_ITEMS.length;
        renderLightbox();
    }

    function renderLightbox(){
        const lb = document.getElementById('mediaLightbox');
        if (!lb) return;
        const content = lb.querySelector('.lightbox-content');
        content.innerHTML = '';
        const item = LB_ITEMS[LB_INDEX];
        if (!item) return;
        if (item.type === 'video'){
            const v = document.createElement('video');
            v.src = item.src;
            v.controls = true;
            v.autoplay = true;
            content.appendChild(v);
        } else {
            const img = document.createElement('img');
            img.src = item.src;
            content.appendChild(img);
        }
        lb.querySelector('.lightbox-counter').textContent = (LB_INDEX+1) + '/' + LB_ITEMS.length;
    }

    // DelegaÃ§Ã£o de clique em .gallery-item
    document.addEventListener('click', function(e){
        const item = e.target.closest('.gallery-item');
        if (!item) return;
        const gallery = item.closest('.post-gallery');
        if (!gallery) return;
        const elems = Array.from(gallery.querySelectorAll('.gallery-item'));
        const items = elems.map(el => {
            const type = el.dataset.type || (el.querySelector('video') ? 'video' : 'imagem');
            const mediaEl = el.querySelector('.gallery-media');
            const src = mediaEl ? (mediaEl.tagName === 'VIDEO' ? mediaEl.currentSrc || mediaEl.src : mediaEl.src) : '';
            return { type, src };
        });
        const start = Number(item.dataset.index) || elems.indexOf(item) || 0;
        openLightbox(items, start);
    });

})();
