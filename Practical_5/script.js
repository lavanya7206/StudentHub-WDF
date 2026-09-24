let next=document.getElementById("next");
let prev=document.getElementById("prev");
let sliderImage=document.getElementById("sliderImage");

if(next&&prev&&sliderImage){
let images=["picture.jpg","Logo_StudentHub.png"];
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
window.onclick=function(e){
if(e.target==modal){
modal.style.display="none";
}
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

if(localStorage.getItem("theme")=="dark"){
document.body.classList.add("dark");
if(themeBtn){
themeBtn.textContent="☀️";
}
}

if(themeBtn){
themeBtn.onclick=function(){
document.body.classList.toggle("dark");
if(document.body.classList.contains("dark")){
localStorage.setItem("theme","dark");
themeBtn.textContent="☀️";
}else{
localStorage.setItem("theme","light");
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

let regForm=document.getElementById("regForm");

if(regForm){
regForm.addEventListener("submit",function(e){
e.preventDefault();

let name=document.getElementById("name").value.trim();
let email=document.getElementById("email").value.trim();
let mobile=document.getElementById("mobile").value.trim();
let password=document.getElementById("password").value;
let confirmPass=document.getElementById("confirmPass").value;
let course=document.getElementById("course").value;
let year=document.querySelector('input[name="year"]:checked');
let gender=document.querySelector('input[name="gender"]:checked');
let terms=document.getElementById("terms").checked;

let namePattern=/^[A-Za-z ]{2,}$/;
let emailPattern=/^[^\s@]+@[^\s@]+\.[^\s@]+$/;
let mobilePattern=/^[0-9]{10}$/;
let passwordPattern=/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*]).{8,}$/;

document.querySelectorAll("small").forEach(function(x){
if(x.id.endsWith("Error")){
x.textContent="";
}
});

document.getElementById("successMsg").textContent="";

let valid=true;

if(!namePattern.test(name)){
document.getElementById("nameError").textContent="Enter a valid name.";
valid=false;
}

if(!emailPattern.test(email)){
document.getElementById("emailError").textContent="Enter a valid email address.";
valid=false;
}

if(!mobilePattern.test(mobile)){
document.getElementById("mobileError").textContent="Enter a valid 10-digit mobile number.";
valid=false;
}

if(!passwordPattern.test(password)){
document.getElementById("passError").textContent="Password must have 8+ characters, uppercase, lowercase, number and special character.";
valid=false;
}

if(password!==confirmPass||confirmPass===""){
document.getElementById("confirmPassError").textContent="Passwords do not match.";
valid=false;
}

if(course===""){
document.getElementById("courseError").textContent="Please select a course.";
valid=false;
}

if(!year){
document.getElementById("yearError").textContent="Please select your year.";
valid=false;
}

if(!gender){
document.getElementById("genderError").textContent="Please select your gender.";
valid=false;
}

if(!terms){
document.getElementById("termsError").textContent="Please accept the terms and conditions.";
valid=false;
}

if(valid){
document.getElementById("successMsg").textContent="Registration successful!";
regForm.reset();
}
});
}