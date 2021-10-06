$(document).ready(function(){
  $(".item-form").on("submit", function (e) {
      let quillEditor = new Quill('.editor-description');
      let value = $('.editor-description > div.ql-editor').html();
      if (quillEditor.getLength() > 1) {
          $(this).append("<textarea name='description' style='display:none'>"+value+"</textarea>");
      }
  });
});