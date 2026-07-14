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
                BOTÃO NOVA POSTAGEM
========================================================== */

if (floatingButton) {

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

}




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

const images=document.querySelectorAll(".post-image, .gallery img, .gallery-item img.gallery-media");

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

if (previewModal) {

    previewModal.addEventListener("click",(e)=>{

        if(e.target===previewModal){

            previewModal.classList.remove("active");

        }

    });

}


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
        GALERIA DE FOTOS/VÍDEOS DO POST (grade estilo Facebook)
========================================================== */

document.querySelectorAll(".post-gallery").forEach(galeria => {

    const itens = Array.from(galeria.querySelectorAll(".gallery-item"));
    const total = itens.length;

    if (total === 0) return;

    const grid = galeria.querySelector(".gallery-grid");
    grid.classList.add("layout-" + Math.min(total, 4));

    // Quando tem mais de 4 mídias, mostra só as 4 primeiras e
    // coloca um "+N" em cima da última visível.
    if (total > 4) {

        itens.forEach((item, i) => {
            if (i >= 4) item.style.display = "none";
        });

        const restante = total - 4;
        const overlay = document.createElement("div");
        overlay.className = "gallery-mais-overlay";
        overlay.textContent = "+" + restante;
        itens[3].appendChild(overlay);

    }

});



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

function formatRelativeTime(dateString) {
    const now = new Date();
    const created = new Date(dateString.replace(' ', 'T'));
    if (isNaN(created)) return 'há pouco';
    const diffMs = now - created;
    const diffSec = Math.floor(diffMs / 1000);
    const diffMin = Math.floor(diffSec / 60);
    const diffH = Math.floor(diffMin / 60);
    const diffD = Math.floor(diffH / 24);
    const diffM = Math.floor(diffD / 30);
    const diffY = Math.floor(diffD / 365);

    if (diffSec < 10) return 'agora mesmo';
    if (diffSec < 60) return 'há poucos segundos';
    if (diffMin < 60) return `há ${diffMin} ${diffMin === 1 ? 'minuto' : 'minutos'}`;
    if (diffH < 24) return `há ${diffH} ${diffH === 1 ? 'hora' : 'horas'}`;
    if (diffD < 30) return `há ${diffD} ${diffD === 1 ? 'dia' : 'dias'}`;
    if (diffM < 12) return `há ${diffM} ${diffM === 1 ? 'mês' : 'meses'}`;
    return `há ${diffY} ${diffY === 1 ? 'ano' : 'anos'}`;
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

function escapeHtml(text) {
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

    article.innerHTML = `
        <div class="post-top">
            <img src="${escapeHtml(post.autor_avatar)}" alt="${escapeHtml(post.autor_nome)}">
            <div>
                <h3>${escapeHtml(post.autor_nome)}</h3>
                <span data-created="${escapeHtml(post.created_at)}">${escapeHtml(post.data_relativa)}</span>
            </div>
        </div>
        <p>${nl2br(post.conteudo)}</p>
        ${post.midia ? (post.midia_tipo === 'video'
            ? `<video class="post-media" src="${escapeHtml(post.midia)}" controls></video>`
            : `<img class="post-media post-image" src="${escapeHtml(post.midia)}" alt="Imagem da postagem">`)
            : ''}
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
            <input type="text" placeholder="Escreva um comentário...">
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
                alert('Digite um comentário.');
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

function bindPostEvents(root) {
    bindLikeButtons(root);
    bindCommentButtons(root);
    bindCommentInputs(root);
    bindShareButtons(root);
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

const openPontosModal = document.getElementById('openPontosModal');
const pontosModal = document.getElementById('pontosModal');
const closePontos = document.querySelector('#pontosModal .close-modal');

if (openPontosModal && pontosModal) {

    openPontosModal.addEventListener('click', function(e) {

        e.preventDefault();

        pontosModal.classList.add('active');

    });

}

if (closePontos && pontosModal) {

    closePontos.addEventListener('click', function() {

        pontosModal.classList.remove('active');

    });

}


document.addEventListener("DOMContentLoaded", () => {
    console.log("Sistema iniciado.");
});








/* ==========================================
        MENU DOS 3 PONTINHOS
========================================== */

document.querySelectorAll(".post-menu-btn").forEach(button => {

    button.addEventListener("click", function(e) {

        e.stopPropagation();

        const menu = this.parentElement;

        menu.classList.toggle("active");

    });

});

document.addEventListener("click", () => {

    document.querySelectorAll(".post-menu").forEach(menu => {

        menu.classList.remove("active");

    });

});



const openSalvosModal = document.getElementById("openSalvosModal");
const salvosModal = document.getElementById("salvosModal");

if (openSalvosModal && salvosModal) {

    openSalvosModal.addEventListener("click", function(e) {

        e.preventDefault();

        salvosModal.classList.add("active");

    });

}






/* ==========================================================
                INICIALIZAÇÃO
========================================================== */

document.addEventListener("DOMContentLoaded", () => {

    console.log("Sistema iniciado.");

});


/* ==========================================================
                    DICA AMBIENTAL (ROTATIVA)
========================================================== */

const mensagensEco = [
    "🌍 Uma garrafa PET pode levar até 450 anos para se decompor.",
    "♻️ Reciclar uma lata de alumínio economiza até 95% da energia necessária para produzir uma nova.",
    "💧 Fechar a torneira ao escovar os dentes pode economizar até 12 litros de água.",
    "🌳 Uma árvore adulta pode absorver cerca de 22 kg de CO₂ por ano.",
    "🔋 Pilhas e baterias nunca devem ser descartadas no lixo comum.",
    "🛍️ Utilizar sacolas reutilizáveis reduz significativamente o consumo de plástico.",
    "🚲 Trocar o carro pela bicicleta em pequenos trajetos reduz a emissão de poluentes.",
    "📄 Reciclar uma tonelada de papel pode salvar até 20 árvores.",
    "🌊 O plástico descartado incorretamente pode chegar aos oceanos e afetar a vida marinha.",
    "🍃 Compostagem transforma resíduos orgânicos em adubo natural.",
    "💡 Lâmpadas LED consomem menos energia e possuem maior durabilidade.",
    "🚿 Reduzir o tempo do banho ajuda a economizar água e energia.",
    "🌱 Plantar árvores ajuda a combater o aquecimento global.",
    "🥤 Canudos descartáveis podem ser substituídos por opções reutilizáveis.",
    "🗑️ Separar corretamente os resíduos facilita a reciclagem.",
    "📱 Celulares antigos devem ser descartados em pontos de coleta eletrônica.",
    "🌞 A energia solar é uma das fontes de energia mais limpas disponíveis.",
    "🚯 Jogar lixo na rua contribui para enchentes e poluição urbana.",
    "🐢 Animais marinhos frequentemente confundem plástico com alimento.",
    "🏞️ Preservar áreas verdes melhora a qualidade do ar nas cidades.",
    "♻️ O vidro pode ser reciclado infinitas vezes sem perder qualidade.",
    "🧴 Embalagens de produtos de limpeza também podem ser recicladas.",
    "🚰 Uma torneira pingando pode desperdiçar dezenas de litros de água por dia.",
    "🌎 Pequenas atitudes sustentáveis fazem grande diferença para o planeta.",
    "🤝 A conscientização ambiental começa com ações individuais e se fortalece na comunidade."
];

const ecoTipText = document.getElementById("ecoTipText");

if (ecoTipText) {

    let indiceEco = Math.floor(Math.random() * mensagensEco.length);
    ecoTipText.textContent = mensagensEco[indiceEco];

    setInterval(() => {

        ecoTipText.classList.add("fade-out");

        setTimeout(() => {

            indiceEco = (indiceEco + 1) % mensagensEco.length;
            ecoTipText.textContent = mensagensEco[indiceEco];
            ecoTipText.classList.remove("fade-out");

        }, 400);

    }, 15000);

}


/* ==========================================================
                    FATOS HISTÓRICOS (ROTATIVOS)
========================================================== */

const fatosHistoricos = [
    "📜 1970 — Foi comemorado o primeiro Dia da Terra, dando início ao movimento ambiental moderno.",
    "🌍 1972 — A Conferência de Estocolmo foi a primeira grande reunião mundial sobre meio ambiente.",
    "♻️ 1988 — O símbolo internacional da reciclagem se popularizou mundialmente.",
    "🌳 1992 — O Brasil sediou a ECO-92 no Rio de Janeiro, um dos maiores eventos ambientais da história.",
    "🌎 1997 — Foi assinado o Protocolo de Kyoto para redução da emissão de gases poluentes.",
    "🌱 2005 — O Protocolo de Kyoto entrou oficialmente em vigor.",
    "🐢 2015 — 193 países aprovaram os Objetivos de Desenvolvimento Sustentável da ONU.",
    "🌡️ 2015 — O Acordo de Paris foi criado para combater as mudanças climáticas.",
    "♻️ 2017 — O Quênia proibiu sacolas plásticas, tornando-se referência mundial.",
    "🐋 1986 — Entrou em vigor a moratória internacional da caça comercial às baleias.",
    "🌳 1962 — O livro 'Primavera Silenciosa' alertou o mundo sobre os impactos dos pesticidas.",
    "🌍 1987 — O Protocolo de Montreal iniciou o combate aos gases que destruíam a camada de ozônio.",
    "☀️ 2016 — A energia solar tornou-se a fonte energética que mais cresceu no mundo.",
    "🌲 2019 — Milhões de pessoas participaram das greves globais pelo clima.",
    "🌎 2022 — O Brasil ultrapassou 20 GW de capacidade instalada em energia solar."
];

const fatoHistorico1 = document.getElementById("fatoHistorico1");
const fatoHistorico2 = document.getElementById("fatoHistorico2");

if (fatoHistorico1 && fatoHistorico2) {

    let indiceFato = Math.floor(Math.random() * fatosHistoricos.length);

    function mostrarFatos(i) {
        fatoHistorico1.textContent = fatosHistoricos[i % fatosHistoricos.length];
        fatoHistorico2.textContent = fatosHistoricos[(i + 1) % fatosHistoricos.length];
    }

    mostrarFatos(indiceFato);

    setInterval(() => {

        fatoHistorico1.classList.add("fade-out");
        fatoHistorico2.classList.add("fade-out");

        setTimeout(() => {

            indiceFato = (indiceFato + 2) % fatosHistoricos.length;
            mostrarFatos(indiceFato);

            fatoHistorico1.classList.remove("fade-out");
            fatoHistorico2.classList.remove("fade-out");

        }, 400);

    }, 15000);

}

/* ==========================================================
                    MODO ESCURO
========================================================== */

const themeToggle = document.getElementById("theme-toggle");

function aplicarIconeTema(escuroAtivo) {
    if (!themeToggle) return;
    const icon = themeToggle.querySelector("i");
    if (escuroAtivo) {
        icon.classList.remove("fa-moon");
        icon.classList.add("fa-sun");
    } else {
        icon.classList.remove("fa-sun");
        icon.classList.add("fa-moon");
    }
}

const temaSalvo = localStorage.getItem("tema");
if (temaSalvo === "dark") {
    document.body.classList.add("dark-mode");
}
aplicarIconeTema(document.body.classList.contains("dark-mode"));

if (themeToggle) {
    themeToggle.addEventListener("click", () => {
        document.body.classList.toggle("dark-mode");
        const escuroAtivo = document.body.classList.contains("dark-mode");
        localStorage.setItem("tema", escuroAtivo ? "dark" : "light");
        aplicarIconeTema(escuroAtivo);
    });
}

/* ==========================================================
                MISSÃO DO DIA (1 por dia, 31 no total)
========================================================== */

const missoesDoDia = [
    "Recolha pelo menos <strong>5 resíduos recicláveis</strong> e compartilhe sua missão no Trash Hunters.",
    "Separe o lixo <strong>orgânico do reciclável</strong> em casa hoje.",
    "Leve suas <strong>pilhas usadas</strong> a um ponto de coleta.",
    "Reutilize uma <strong>embalagem</strong> em vez de jogar fora.",
    "Ande a pé ou de bicicleta em vez de usar o <strong>carro</strong> hoje.",
    "Recolha <strong>3 garrafas PET</strong> pelo caminho e destine à reciclagem.",
    "Plante ou regue uma <strong>muda de árvore</strong>.",
    "Evite usar <strong>sacolas plásticas</strong> nas compras de hoje.",
    "Limpe um <strong>espaço público</strong> perto de você (praça, calçada, praia).",
    "Separe <strong>papelão</strong> para reciclagem.",
    "Doe uma <strong>peça de roupa</strong> que você não usa mais.",
    "Reduza o tempo do <strong>banho em 2 minutos</strong> hoje.",
    "Feche a <strong>torneira</strong> ao escovar os dentes.",
    "Leve um <strong>copo reutilizável</strong> em vez de descartável.",
    "Recolha e descarte corretamente <strong>1 pilha ou bateria</strong>.",
    "Compartilhe uma <strong>dica ambiental</strong> com um amigo.",
    "Separe <strong>vidro</strong> para reciclagem.",
    "Evite <strong>imprimir</strong> documentos desnecessários hoje.",
    "Leve <strong>eletrônicos antigos</strong> a um ponto de coleta eletrônica.",
    "Recolha lixo encontrado em uma <strong>trilha ou parque</strong>.",
    "Use as <strong>duas faces do papel</strong> antes de descartar.",
    "Desligue aparelhos da <strong>tomada</strong> quando não estiver usando.",
    "Recolha ao menos <strong>5 resíduos recicláveis</strong> pelo bairro.",
    "Plante uma <strong>horta ou vaso</strong> em casa.",
    "Evite o <strong>desperdício de comida</strong> hoje.",
    "Compre de um <strong>produtor local</strong> ou orgânico.",
    "Recicle uma <strong>caixa de papelão</strong>.",
    "Ajude a organizar uma <strong>coleta seletiva</strong> no seu prédio ou rua.",
    "Reduza o uso de <strong>canudos e talheres descartáveis</strong> hoje.",
    "Publique uma foto de uma <strong>ação sustentável</strong> no Trash Hunters.",
    "Faça um balanço do mês: <strong>quantos resíduos</strong> você recolheu?"
];

const missaoTexto = document.getElementById("missaoDoDiaTexto");

if (missaoTexto) {

    function atualizarMissaoDoDia() {
        const hoje = new Date();
        const dia = hoje.getDate(); // 1 a 31
        missaoTexto.innerHTML = missoesDoDia[dia - 1];
    }

    atualizarMissaoDoDia();

    // Confere a cada minuto se o dia virou, pra trocar sozinho sem precisar recarregar
    let diaAtual = new Date().getDate();

    setInterval(() => {

        const diaAgora = new Date().getDate();

        if (diaAgora !== diaAtual) {
            diaAtual = diaAgora;
            atualizarMissaoDoDia();
        }

    }, 60000);

}