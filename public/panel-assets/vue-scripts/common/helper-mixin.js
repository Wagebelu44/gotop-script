let mixin = {
    data(){
      return {
        loader: false,
      }
    },
    methods: {
      foo () {
        console.log('foo')
      },
      conflicting () {
        console.log('from mixin')
      },
      getParameterByName(name, url) {
            if (!url) url = window.location.href;
            name = name.replace(/[\[\]]/g, '\\$&');
            var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
                results = regex.exec(url);
            if (!results) return null;
            if (!results[2]) return '';
            return decodeURIComponent(results[2].replace(/\+/g, ' '));
        },
    }
  }