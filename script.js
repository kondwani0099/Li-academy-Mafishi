const chatInput = document.querySelector("#chat-input");
const sendButton = document.querySelector("#send-btn");
const chatContainer = document.querySelector(".chat-container");
const themeButton = document.querySelector("#theme-btn");
const deleteButton = document.querySelector("#delete-btn");

let userText = null;
const API_KEY = "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";
const initialHeight = chatInput.scrollHeight;



const loadDataFromLocalstorage = () => {
    const themeColor = localStorage.getItem("theme-color");
    document.body.classList.toggle("light-mode", themeColor === "light-mode");
    themeButton.innerText = document.body.classList.contains("light-mode") ? "dark_mode" : "light_mode";

    const defaultText = `<div class ="default-text">
                         <h1> MAFISHI - AI </h>
                         <p> Unlimited Information At The Palm Of Your Hands. <br> Your Chat will be displayed here.</p>
                        </div>`
    chatContainer.innerHTML = localStorage.getItem("all-chats") || defaultText;
    chatContainer.scrollTo(0, chatContainer.scrollHeight);
}
loadDataFromLocalstorage();

const createElement = (html, className) => {
    // Create new div and apply chat, specified class and set html content of div
    const chatDiv = document.createElement("div");
    chatDiv.classList.add("chat", className);
    chatDiv.innerHTML = html;
    return chatDiv;
}

const getChatResponse = async (incomingChatDiv) => {
    const API_URL = "https://api.openai.com/v1/completions";
    const pElement = document.createElement("p");

// Define properties and data for the API request
    const requestOptions = {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Authorization": `Bearer ${API_KEY}`
        },
        body: JSON.stringify({
            model: "gpt-3.5-turbo-instruct",                 
            prompt: userText,
            max_tokens: 2048,
            temperature: 0.1,
            top_p: 1,
            n: 1,
            stop: null
        })
    }
// Send POST request to API, get response and set the response as paragraph element text
    try {
        const response = await (await fetch(API_URL, requestOptions)).json();
        pElement.textContent  = response.choices[0].text.trim();
    } catch(error) {
        console.log(error);
    }

    incomingChatDiv.querySelector(".typing-animation").remove();
    incomingChatDiv.querySelector(".chat-details").appendChild(pElement);
    chatContainer.scrollTo(0, chatContainer.scrollHeight);
    localStorage.setItem("all-chats", chatContainer.innerHTML);
}

const copyResponse = (copyBtn) => {
    const responseTextElement = copyBtn.parentElement.querySelector("p");
    navigator.clipboard.writeText(responseTextElement.textContent);
    copyBtn.textContent = "done";
    setTimeout(() => copyBtn.textContent = "content_copy", 1000);
}


const showTypingAnimation = () => {
    const html = ` <div class="chat-incoming">
    <div class="chat-content">
        <div class="chat-details">
            <img src="../Li-Academy/images/logo.png" alt="img-user"> 
            <div class="typing-animation">
                <div class="typing-dot" style="--delay: 0.2s"></div>
                <div class="typing-dot" style="--delay: 0.3s"></div>
                <div class="typing-dot" style="--delay: 0.4s"></div>
            </div>
        </div>
        <span onclick="copyResponse(this)" class="material-symbols-rounded">content_copy</span>
    </div>
</div> `;
const incomingChatDiv = createElement(html, "incoming");
chatContainer.appendChild(incomingChatDiv);
chatContainer.scrollTo(0, chatContainer.scrollHeight);
getChatResponse(incomingChatDiv);
}

// declaring function "handleOutgoingChat"
const handleOutgoingChat = () => {
    userText = chatInput.value.trim(); // Get chatInput vaue and remove extra spaces
    if(!userText) return; // If chatInput is empty return from here

    
    chatInput.value = "";
    chatInput.style.height = `${initialHeight}px`;
    const html = `<div class="chat-outgoing">
    <div class="chat-content">
        <div class="chat-details">
            <img src="../Li-Academy/images/pretty2.jpg" alt="img-user"> 
            <p>${userText}</p>
        </div>
    </div>
</div>`;
const outgoingChatDiv = createElement(html, "outgoing");
document.querySelector(".default-text")?.remove();
chatContainer.appendChild(outgoingChatDiv);
chatContainer.scrollTo(0, chatContainer.scrollHeight);
setTimeout(showTypingAnimation, 500);

}

themeButton.addEventListener("click", () => {
    // Toggle body's class for the theme mode and save the updated theme to local storage
    document.body.classList.toggle("light-mode");
    localStorage.getItem("theme-color", themeButton.innerText);
    themeButton.innerText = document.body.classList.contains("light-mode") ? "dark_mode" : "light_mode";
}); 

deleteButton.addEventListener("click", () => {
    if(confirm("Are you sure you want to delete all chats?")) {
        localStorage.removeItem("all-chats");
        loadDataFromLocalstorage();
    }
});

chatInput.addEventListener("input", () => {
    //Adjust the height of the input field dynamically based on its content
    chatInput.style.height = `${initialHeight}px`;
    chatInput.style.height = `${chatInput.scrollHeight}px`;
});

chatInput.addEventListener("keydown", (e) => {
    //If the enter key is pressed withut shift and the window is larger than 800 pixels,
    //handle the outgoing chat
    if(e.key === "Enter" && !e.shiftKey && window.innerWidth > 800) {
        e.preventDefault();
        handleOutgoingChat();
    }
});
sendButton.addEventListener("click", handleOutgoingChat);