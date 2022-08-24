tinymce.init({
  selector: "textarea.fullhtmleditor",
  plugins: "advlist link lists image code media table fullscreen quickbars",
});

tinymce.init({
  selector: "textarea.litehtmleditor",
  plugins: "link table",
  menubar: false,
  automatic_uploads: false,
  statusbar: false,
  toolbar:
    "undo redo | h3 h4 h5 | bold italic | link | strikethrough superscript subscript | table",
  table_toolbar: true,
  valid_elements:
    "p,i/em,hr,a[href|target=_blank],strong/b,div[align],br,h3,h4,h5,table,th,tr,td,tbody",
  style_formats: [
    {
      title: "Headings",
      items: [
        { title: "Heading", format: "h3" },
        { title: "Sub-Heading", format: "h4" },
      ],
    },
    {
      title: "Inline",
      items: [
        { title: "Bold", format: "bold" },
        { title: "Italic", format: "italic" },
        { title: "Underline", format: "underline" },
        { title: "Strikethrough", format: "strikethrough" },
        { title: "Superscript", format: "superscript" },
        { title: "Subscript", format: "subscript" },
        { title: "Code", format: "code" },
      ],
    },
    { title: "Blocks", items: [{ title: "Paragraph", format: "p" }] },
  ],
});

tinymce.init({
  selector: "textarea.richtext",
  plugins: "link quickbars",
  menubar: false,
  toolbar: false,
  quickbars_insert_toolbar: false,
  quickbars_selection_toolbar: "bold italic strikethrough | quicklink",
  statusbar: false,
  automatic_uploads: false,
  valid_elements: "p,i/em,a[href|target=_blank],strong/b,br",
  style_formats: [
    {
      title: "Inline",
      items: [
        { title: "Bold", format: "bold" },
        { title: "Italic", format: "italic" },
        { title: "Underline", format: "underline" },
        { title: "Strikethrough", format: "strikethrough" },
      ],
    },
  ],
  // Annoying work-around for required=true fields:
	// First any time the editor changes, save the updates instantly to the form:
  setup: function (editor) {
    editor.on("change", function (e) {
      editor.save();
    });
  },

  
  init_instance_callback: (editor) => {
    var originalElement = editor.getElement();
    if (originalElement.required) {
	  // Remove the 'required' HTML5 attr, to prevent browser errors on a hidden element:
      editor._required = true;
      originalElement.required = false;

	  // Next add our own required message using TinyMCE notifications:
      editor.on("blur", (e) => {
        if (!editor.getContent()) {
          editor.notificationManager.open({
            text: "This field is required",
            type: "error",
            closeButton: false,
          });
        }
      });
      editor.on("focus", (e) => {
        editor.notificationManager.close();
      });
    }
  },
});
