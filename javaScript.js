function selected(element) {
    var remove = document.getElementsByClassName("selectedTid");
    while (remove.length){
        remove[0].classList.remove("selectedTid");
    }

    if (element.classList.contains("selectedTid")){
        element.classList.remove('selectedTid');
    }else{
        element.classList.add('selectedTid');
    }
}