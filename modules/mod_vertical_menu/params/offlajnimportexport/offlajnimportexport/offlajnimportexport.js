
dojo.declare("OfflajnImportExport", null, {
	constructor: function(args) {
    dojo.mixin(this, args);
    this.input = dojo.byId(this.id);
    this.importBtn = dojo.byId(this.id + 'import');
    this.exportBtn = dojo.byId(this.id + 'export');
    dojo.connect(this.input, 'change', dojo.hitch(this, 'upload'));
    dojo.connect(this.importBtn, 'click', dojo.hitch(this, 'import'));
    dojo.connect(this.exportBtn, 'click', dojo.hitch(this, 'export'));
  },

  upload: function() {
    // file check
    var file = this.input.files[0];
    if (!file)
      return alert('Please choose an import file!');
    if (!file.name.match(/\.zip$/i))
      return alert('Wrong file extension!\nPlease choose a ZIP file.');

    if (confirm('Are you sure you want to override your currect parameters?')) {
      var form = jQuery('<form>').attr({
        action: location.href + '&task=offlajnimport',
        method: 'post',
        enctype: 'multipart/form-data'
      }).appendTo('body');
      jQuery(this.input).appendTo(form);
      form[0].submit();
    }
  },

  import: function(e) {
    e.preventDefault();
    this.input.files[0] ? this.upload() : this.input.click();
  },

  export: function(e) {
    e.preventDefault();
    var exclude = new RegExp('^'+ this.exclude.replace(/\s+/g, '|') +'$', 'i');
    // get params
    var m, params = { originalId: location.href.match(/[&\?]id=(\d+)/)[1] };
    jQuery('.panelform :input[name$="]"]').each(function(i, input) {
      if (m = input.name.match(/^jform\[params\]\[(\w+Tab)\](\[\w+\])?\[(\w+)]/)) {
        if (!params[ m[1] ]) params[ m[1] ] = {};
        if (m[3].match(exclude)) return; // continue if excluded
        if (m[2]) {
          m[2] = m[2].substr(1, m[2].length - 2); // remove []
          if (!params[ m[1] ][ m[2] ]) params[ m[1] ][ m[2] ] = {};
          params[ m[1] ][ m[2] ][ m[3] ] = input.value;
        } else {
          params[ m[1] ][ m[3] ] = input.value;
        }
      }
    });
    this.params = params;
    // get images
    var images = [];
    jQuery('.panelform input[data-folder]').each(function(i, input) {
      var folder = input.attributes['data-folder'].value.replace(/\\/g, '/');
      if (input.value.match(/\.(gif|png|bmp|jpg)/i)) images.push({
        url: input.value,
        path: folder,
        file: input.value.match(new RegExp(folder + '/?(.*)$'))[1]
      });
    });
    this.images = images;
    // load zip lib if not exists
    var url = this.modPath + 'params/offlajnimportexport/offlajnimportexport/jszip.min.js';
    window.JSZip ? this.createZip() : jQuery.getScript(url, dojo.hitch(this, 'createZip'));
  },

  createZip: function() {
    this.zip = new JSZip();
    this.zip.file('module.json', JSON.stringify(this.params));
    this.images.length ? this.loadImages(0) : this.download();
  },

  loadImages: function(i) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', this.images[i].url, true);
    xhr.responseType = 'arraybuffer';
    xhr.onreadystatechange = function() {
      if (xhr.readyState == 4) {
        if (xhr.status == 200 && xhr.response !== null) {
          this.zip.file('images/' + this.images[i].file, xhr.response);
          delete this.images[i].url;
        } else {
          delete this.images[i];
        }
        if (++i < this.images.length) {
          this.loadImages(i)
        } else {
          this.images.length && this.zip.file('images.json', JSON.stringify(this.images)); // map images
          this.download();
        }
      }
    }.bind(this);
    xhr.send();
  },

  download: function() {
    var date = new Date().toISOString().slice(0, 10);
    var a = jQuery('<a>').attr({
      download: this.downloadName + date + '.zip',
      href: 'data:application/zip;base64,' + this.zip.generate({ type: 'base64' })
    }).appendTo('body');
    a[0].click();
    a.remove();
  }

});
