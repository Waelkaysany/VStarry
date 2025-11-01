function page2animation(){
   
  var rightElems=document.querySelectorAll(".right-elem");

rightElems.forEach((elem)=>{
elem.addEventListener("mouseenter",()=>{
gsap.to(elem.childNodes[3],{opacity:1,scale:1});})

 
elem.addEventListener("mouseleave",()=>{   /*dets bhala tgol ihdatiyat dyal mouse*/
  gsap.to(elem.childNodes[3],{scale:0,opacity:0});
})
elem.addEventListener("mousemove",(dets)=>{
  gsap.to(elem.childNodes[3],{
  x:dets.x-elem.getBoundingClientRect().x-50,
y:dets.y-elem.getBoundingClientRect().y-130}); 
})
})
 
}
page2animation();

function page3animation(){
  var page3Center=document.querySelector(".page3-center");
var video=document.querySelector("#page3 video");

page3Center.addEventListener("click",()=>{
  video.play();
  gsap.to(video,{
    transform:"scaleX(1) scaleY(1.1)",
    opacity:1,
    borderRadius:0,
    duration:0.5
  })
})
video.addEventListener("click", ()=>{
   video.pause();
   gsap.to(video,{
    transform:"scaleX(0.7) scaleY(0)",
    opacity:0,
    borderRadius:"30px",
    duration:0.5 
  })
})
}
page3animation();
function page3videoanimation(){
  var sections =document.querySelectorAll(".sec-right");
sections.forEach((elem)=>{
  elem.addEventListener("mouseenter",()=>{
    elem.childNodes[3].style.opacity=1;
   elem.childNodes[3].play();
  }
    
  )
})
sections.forEach((elem)=>{
  elem.addEventListener("mouseleave",()=>{
    elem.childNodes[3].style.opacity=0;
   elem.childNodes[3].load();
  }
    
  )
})
}
page3videoanimation();


function page6animation(){
  gsap.from("#btm6-part2 h4",{
  x:0,
  duration:1,
  scrollTrigger:{
    trigger:"#btm6-part2",
    scroller:"body",
   // markers:true,
    start:"top 50%",
    end:"top 10%",
    scrub:true
  }
})
}
page6animation();
function page6animationn(){
  gsap.from("#btm6-part3 h4",{
  x:0,
  duration:1,
  scrollTrigger:{
    trigger:"#btm6-part3",
    scroller:"body",
   // markers:true,
    start:"top 50%",
    end:"top 10%",
    scrub:true
  }
})
}
page6animationn();
function page6animationnn(){
  gsap.from("#btm6-part4 h4",{
  x:0,
  duration:1,
  scrollTrigger:{
    trigger:"#btm6-part4",
    scroller:"body",
   // markers:true,
    start:"top 50%",
    end:"top 10%",
    scrub:true
  }
})
}
page6animationnn();
function loadingpage1(){
  var tl=gsap.timeline()
tl.from("#page1",{
  opacity:0,
  duration:0.3,
  delay:0.2
})
tl.from("#page1",{
  transform:"scaleX(0.7) scaleY(80%)",
  borderRadius:"100px",
  duration:2,
  ease:"expo.out"
})
tl.from("nav ",{
  opacity:0,
  delay:-0.2
})
tl.from("#page1 h1,#page1 p ,#page1 div",{
  opacity:0,
  duration:0.5,
  stagger:0.2
})
}
loadingpage1();
//with ai now





document.addEventListener('DOMContentLoaded', function() {
  // Cookie accept functionality
  const acceptButton = document.querySelector('.accept-button');
  const cookieNotice = document.querySelector('.cookie-notice');
  
  acceptButton.addEventListener('click', function() {
    cookieNotice.style.opacity = '0.5';
    setTimeout(() => {
      cookieNotice.style.display = 'none';
    }, 300);
  });

  // Service item hover effects
  const serviceItems = document.querySelectorAll('.service-item');
  serviceItems.forEach(item => {
    item.addEventListener('mouseenter', function() {
      this.style.transform = 'translateX(8px)';
    });
    
    item.addEventListener('mouseleave', function() {
      this.style.transform = 'translateX(0)';
    });
  });

  // Learn more functionality
  const learnMore = document.querySelector('.cookie-learn-more');
  learnMore.addEventListener('click', function() {
    alert('Cookie Policy: We use cookies to enhance your browsing experience and provide personalized content.');
  });
});