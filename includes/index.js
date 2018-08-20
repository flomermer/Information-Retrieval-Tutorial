$("document").ready(function () {
    $("#formSearch").on("submit", search);
    $("#btnHelp").click(help);
    $("#searchResults").on("click", ".result", showDocument);    
})

function help() {
    $("#modalHelp").modal("show");
}
function search(event) {
    event.preventDefault();
    var keyword = $("#txtSearch").val();
    if (keyword == '') {
        $("#searchResults").html("<h4>Please enter keyword...</h4>");
        return false;
    }
    $.ajax({
        type: "GET",
        data: { keyword: keyword },
        url: "server/searchAPI.php",
        success: function (json) {                        
            console.log(json);
            renderResults(json);
            colorizeWords(json);
        }, error: function () {                 
            $("#searchResults").html("<h4>Error. Check your syntax.</h4>");
        }
    });
}

function renderResults(json) {
    $("#searchResults").empty();    
    var $ul = $("<ul class='list-group'></ul>");
    if (json.length == 0) { //no records found
        $("#searchResults").html("<h3>No Records Found");
        return false;
    }
    $.map(json, function (doc) {
        var $li = $("<li class='list-group-item result' id='doc" + doc.documentID + "'><span>" + doc.name + " - " + doc.author + "</span></li>");
        $li.data("key", doc);
        
        var $subLi = $("<UL></UL>");
        $subLi.append("<li>wordsCounter: " + doc.wordsCounter + " (" + doc.words + ")</li>");
        $subLi.append("<li>totalTimes: " + doc.times + "</li>");
        $subLi.append('<li class="content"><q>' + doc.content.substring(0,200) + ' ...</q></li>');
        $li.append($subLi);
        $ul.append($li);
    })
    $("#searchResults").append($ul);

    colorizeWords();
}
function colorizeWords(json){    
    $.map(json, function (doc) {
        var arr = doc.words.split(",");
        arr.sort(function (a, b) {
            return b.length - a.length;
        });
        $.each(arr, function (index, word) {            
            $("#doc" + doc.documentID + " .content").highlight(word);
        })            
    })
}
function showDocument() {
    var doc = $(this).data("key");
    var keyword = $("#txtSearch").val().replace(/\"/g, "");    
    window.open("document.php?document_id=" + doc.documentID + "&words="+doc.words);
}