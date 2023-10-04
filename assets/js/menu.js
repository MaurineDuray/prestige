console.log('menu')
const body = document.body

const burger = document.querySelector("#burger")
const menuResponsive = document.querySelector('#menuResponsive')

const items = document.querySelectorAll('#menuResponsive ul li')
console.log(items)



// gestion du menu burger 
burger.addEventListener('click', function(){
    console.log('click menu')
    let openned = menuResponsive.getAttribute('class')
    if(openned=='open'){
        menuResponsive.classList.remove('open')
        burger.classList.remove('open');
        menuResponsive.style.transition="all 1s";
    }else{
        menuResponsive.classList.add('open')
        burger.classList.add('open');
        menuResponsive.style.transition="all 1s";
    }
   
})

// gestion de changement de pages menu 
items.forEach(item=>{
    item.addEventListener('click', function(){
    let openned = menuResponsive.getAttribute('class')
    if(openned=='open'){
        menuResponsive.classList.remove('open')
        burger.classList.remove('open');
        menuResponsive.style.transition="all 1s";
    }else{
        menuResponsive.classList.add('open')
        burger.classList.add('open');
        menuResponsive.style.transition="all 1s";
    }
})
})


let login = document.querySelector('#myaccount')
let accountBlock = document.querySelector('.accountBlock')
console.log(accountBlock)

login.addEventListener('click', function(){
    console.log('login click')
    accountBlock.classList.toggle('accountopen')
})

const btnCookies = document.querySelector('#btnCookies')
const cookies = document.querySelector('.cookies')

btnCookies.addEventListener('click',()=>{
    cookies.style.display="none"
    localStorage.setItem('cookies',"true")
})
if (localStorage.getItem('cookies')=="true") {
    cookies.style.display="none"
}else{
    cookies.style.display="block"
}
