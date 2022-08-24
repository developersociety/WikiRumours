tinymce.init({
  selector: "textarea.fullhtmleditor",
  plugins: "advlist link lists image code media table fullscreen quickbars",
});

tinymce.init({
  selector: "textarea.litehtmleditor",
  plugins: "link",
  menubar: false,
  automatic_uploads: false,
  statusbar: false,
  toolbar: 'undo redo | h3 h4 h5 | bold italic | link | strikethrough superscript subscript',
  valid_elements:
    "p,i/em,hr,a[href|target=_blank],strong/b,div[align],br,h3,h4,h5",
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
  quickbars_selection_toolbar: 'bold italic strikethrough | quicklink',
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
});
