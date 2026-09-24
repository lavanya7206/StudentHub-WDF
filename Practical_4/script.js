let next=document.getElementById("next");
let prev=document.getElementById("prev");
let sliderImage=document.getElementById("sliderImage");

if(next&&prev&&sliderImage){
let images=[
"picture.jpg",
"Logo_StudentHub.png"
];
let i=0;

next.onclick=function(){
i=(i+1)%images.length;
sliderImage.src=images[i];
};

prev.onclick=function(){
i=(i-1+images.length)%images.length;
sliderImage.src=images[i];
};
}

document.querySelectorAll(".faq-item h3").forEach(function(q){
q.onclick=function(){
this.parentElement.classList.toggle("active");
};
});

let openModal=document.getElementById("openModal");
let closeModal=document.getElementById("closeModal");
let modal=document.getElementById("modal");

if(openModal&&closeModal&&modal){
openModal.onclick=function(){
modal.style.display="block";
};

closeModal.onclick=function(){
modal.style.display="none";
};
}

let menuBtn=document.getElementById("menuBtn");
let navMenu=document.getElementById("navMenu");

if(menuBtn&&navMenu){
menuBtn.onclick=function(){
navMenu.classList.toggle("show");
};
}

let themeBtn=document.getElementById("themeBtn");

if(themeBtn){
themeBtn.onclick=function(){
document.body.classList.toggle("dark");

if(document.body.classList.contains("dark")){
themeBtn.textContent="☀️";
}else{
themeBtn.textContent="🌙";
}
};
}

let notification=document.getElementById("notification");

if(notification){
setTimeout(function(){
notification.style.display="none";
},4000);
}