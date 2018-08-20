$("document").ready(function () {   
    $("#eye").click(eyeClick);
    $(".document[data-hidden=1]").addClass("strokeLine");

    $("#btnUpload").click(uploadFileClick);
    $("#inputUpload").change(openUploadModal);
    $("#formDocument").on("submit", formGo);

    $("#documentsList").on("click", ".document", openDocumentModal);    
})

function eyeClick() {     
    var document_id = $("#formDocument input[name=documentID]").val();
    var doc = $(".document[data-id=" + document_id + "]").data();
    var newVal;
    if (doc.hidden)
        newVal = 0;        
    else
        newVal = 1;          
    
    $.ajax({
        type: "GET",
        url: "server/set/setDynamicField.php?tableName=documents&field=isHidden&val=" + newVal + "&whereField=document_id&whereVal=" + doc.id,
        success: function (data) {
            doc.hidden = newVal;
            if (doc.hidden) {
                $("#eye svg").removeClass("fa-eye").addClass("fa-eye-slash");
                $(".document[data-id=" + doc.id + "]").addClass("strokeLine");
            } else {
                $("#eye svg").addClass("fa-eye").removeClass("fa-eye-slash");
                $(".document[data-id=" + doc.id + "]").removeClass("strokeLine");
            }
        }
    })
    
}

function openDocumentModal() {
    var doc = $(this).data();

    $("#eye").show();
    if (doc.hidden) {
        $("#eye svg").removeClass("fa-eye").addClass("fa-eye-slash");        
    } else {
        $("#eye svg").addClass("fa-eye").removeClass("fa-eye-slash");        
    }
    
    $("#modal .modal-header label").html("<a href='document.php?document_id="+doc.id+"' target='_blank'>" + doc.id + ". " + doc.filename + "</a>");
    $("#formDocument .btn-submit").show().text("Update Document");
    $("#formDocument .loading").hide();
    $("#formDocument input[name=name]").val(doc.name);
    $("#formDocument input[name=author]").val(doc.author);
    $("#formDocument input[name=filename]").val(doc.filename);
    $("#formDocument input[name=documentID]").val(doc.id);
    $("#modal").modal("show");
}

function uploadFileClick() {
    $("#inputUpload").click();
}
function openUploadModal() {
    var filename = $(this).val().split('\\').pop();
    if (filename == '')
        return false;

    if ($(".document[data-filename='" + filename + "']").length) { //already exists in documents
        alert("document is already exists");
        return false;
    }

    $("#eye").hide();
    $("#formDocument").trigger("reset");
    $("#modal .modal-header label").text(filename);
    $("#formDocument input[name=filename]").val(filename);
    $("#formDocument")
    $("#inputUpload").val('');

    $("#formDocument .btn-submit").show().text("Index File");
    $("#formDocument .loading").hide();

    $("#modal").modal("show");
}
function formGo(event) {
    event.preventDefault();
    var formData = $("#formDocument").serialize();
    
    $("#formDocument .btn-submit").hide();
    $("#formDocument .loading").show();

    var author, name, filename;
    name = $("#formDocument input[name=name]").val();
    author = $("#formDocument input[name=author]").val();
    filename = $("#formDocument input[name=filename]").val();
            
    $.ajax({
        type: "GET",
        data: formData,
        url: "server/indexFile.php",
        success: function (documentID) {            
            if ($(".document[data-id=" + documentID + "]").length){ //edit exists file
                var $line = $(".document[data-id=" + documentID + "]");
                $line.data({ "name": name, "author": author }).text(documentID + ". " + name + " - " + author + " - " + filename);
            } else { //index new file
                window.location.reload();
            }            
            $("#modal").modal("hide");
            $("#formDocument .btn-submit").show();
            $("#formDocument .loading").hide();
        }, error: function () {
            alert("error");
        }
    })
}
