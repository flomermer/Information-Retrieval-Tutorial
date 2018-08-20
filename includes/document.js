$("document").ready(function(){
    colorizeWords();
});

function colorizeWords() {
    var words = $("#words").val();    
    var arr = words.split(",");    
    arr.sort(function (a, b) {        
        return b.length - a.length;
    });
    $.each(arr, function (index, word) {                                
        $("#document").highlight(word);  
    })    
}