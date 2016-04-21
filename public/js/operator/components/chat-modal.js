
  new Vue({
    el: '#vueScope'
  });
  var ref = new Wilddog("https://buypal.wilddogio.com/");
  var userRef = ref.child('users');


  Vue.component('chat-modal', {
    template: '#chat-template',
    data: function() {
      return {
        users: [],
        chatChecking: {}
      }
    },
    methods: {
      toggleList: function() {
        $('.chat_body').slideToggle('slow');
      },
      toggleMsg: function() {
        $('.msg_wrap').slideToggle('slow');
      },
      closeMsg: function() {
        $('.msg_box').hide();
      },
      showMsg: function(chat) {
        $('.msg_wrap').show();
        $('.msg_box').show();
        this.chatChecking = chat;
      },
      toDealIt: function(chat) {
        userRef.child(chat.open_id).remove();
        this.users.$remove(chat);
        $('.msg_box').hide();
      },
      getObjectLength: function(ob) {
        return Object.keys(ob).length;
      }
    },
    created: function() {
      var that = this;

      userRef.on('child_changed', function(data) {
        console.log(data.val(), data.key());
      });

      userRef.on('child_added', function(data) {
        var user = data.val();
        user.open_id = data.key();
        that.users.push(user);
      });

      userRef.on('child_removed', function(data) {
        that.users.$remove(data.val())
      })
    }
  });
