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
