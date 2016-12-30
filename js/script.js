jQuery(document).ready(function($) {

  var forum_actions = {

    init: function() {

      this.fire_accordion();
      this.reply_form();
      this.comment_reply();
      this.add_topic_form();
      this.topic_submit_action();



    },

    fire_accordion: function() {

        if ($( "#accordion_qa_forum" ).length === 0)
          return;

          $( "#accordion_qa_forum" ).css('display', 'none');

          //$( "#accordion_qa_forum" ).html("tetet ");

          var data = {
    				'action': 'request_forum_data'
    			};


          $.ajax({
            type: 'POST',
            url: plugin_data.ajax_url,
            data: data,
            async: false
          }).done(function(response) {

              if (response == "false") {
                location.reload();
                return;
              }

              response = JSON.parse(response);
              var data = "";

              var titles = response.titles;
              var forum_topic_posts = response.forum_topic_posts;

              $.each(forum_topic_posts, function (i, forum_topic_post) {

                //console.log(forum_topic_post);
                //console.log(typeof forum_topic_posts.post_title);

                data += "<h3 data-title='"+forum_topic_post.post_title+"'>"+forum_topic_post.post_title+"</h3>";
                data += ' <div data-title="'+forum_topic_post.post_title+'" data-topic_id="'+forum_topic_post.ID+'" ><p>'+forum_topic_post.post_content+'</p>';
                data += '</div>';

              })

              $( "#accordion_qa_forum" ).html(data)
                .accordion({
                    collapsible: true
                  });


                  ///load comments

                  $("div[data-topic_id]").each(function(index) {

                    $(this).css("height", "auto");
                    //console.log($(this).data('topic_id'));

                    var data = {
                      'action' : 'request_forum_comments',
                      'post_id' : $(this).data('topic_id')

                    };

                    //'post_id' : $(this).data('topic_id')
                    var the_thread = $(this);
                    var the_thread_id = $(this).data('topic_id');
                    var the_thread_title = $(this).data('title');

                    $.ajaxSetup({async: false});

                    jQuery.post(plugin_data.ajax_url, data, function(response) {

                      if (response.length === 0)
                        return;

                        response = JSON.parse(response);

                        var html = "";

                        html += '<div data-common_sub_class="accordion_qa_forum_sub" data-unique_tag="accordion_qa_forum_sub_'+the_thread_id+'">';

                        $.each(response, function(i, content) {
                          html += "<div class='parent_comment' data-stat='parent_comment' data-post_id="+content.comment_post_ID+" data-comment_id="+content.comment_ID+" data-comment_author="+content.comment_author+">";
                          html += "<strong><em>"+content.comment_author+": </strong></em><br>";
                          html += content.comment_content
                          html += '<span data-comment_author="'+content.comment_author+'" data-comment_id="'+content.comment_ID+'" class="comment_reply"><a href="#">Reply</a> | <span class="comment_time">'+content.comment_date+'</span></span>';
                          html += "</div>";

                        })

                        html += '</div>';

                        var main_data = the_thread.html();
                        the_thread.html(main_data+html);


                     }).always(function() {


                       var html_reply = "";

                         html_reply += '<div data-thread_id="'+the_thread_id+'" data-common_sub_class="accordion_qa_forum_sub_reply" data-unique_tag="accordion_qa_forum_sub_reply_'+the_thread_id+'">';

                         html_reply += "<div data-comment_author=''>";
                         html_reply += "<div class='status'></div>";

                         html_reply += "<strong><em>Reply:</strong></em><br>";


                         html_reply += '<br><textarea rows="4" cols="50" name="comment" placeholder="Replying on '+the_thread_title+'" form="thread_reply_form"></textarea>';

                         html_reply += '<br><br><form data-parent_comment_id="" data-thread_id="'+the_thread_id+'" data-replying_to_user="" action="" name="thread_reply_form"><input type="submit" value="Reply"></form>';


                         html_reply += "</div>";


                       html_reply += '</div>';

                       the_thread.append(html_reply);
                       //console.log("always");

                     });


                     $.ajaxSetup({async: true}); //So as to avoid any other ajax calls made sybchrounously

                  })


                  //forum_actions.load_child_comments();


          }).always(function() {


            forum_actions.load_child_comments();

          })


          // jQuery.post(plugin_data.ajax_url, data, function(response) {
          //
          // });

          $( "#accordion_qa_forum" ).css('display', 'block');


    },

    reply_form: function () {

      $(document).on('submit', "form[name='thread_reply_form']", function(evt) {

        evt.preventDefault();

        var reply_val = $(this).siblings("textarea").val();

        var thread_id = $(this).data("thread_id");
        var replying_to_user = $(this).data("replying_to_user");
        var parent_comment_id = $(this).data("parent_comment_id");

        console.log(parent_comment_id);

        var data = {
          'action' : 'replying_forum_thread',
          'post_id' : thread_id,
          'reply_val' : reply_val,
          'replying_to_user' : replying_to_user,
          'parent_comment_id' : parent_comment_id
        };


        var the_form_el = $(this);

        jQuery.post(plugin_data.ajax_url, data, function(response) {

            if(response > 0)
              the_form_el.siblings(".status").html("<span style='color: green'>Success!</span>");
            else
              the_form_el.siblings(".status").html("<span style='color: red'>Failed!</span>");

              location.reload();
         });


      })


    },

    comment_reply: function() {

      $(document).on("click", ".comment_reply", function(event) {

        event.preventDefault();

        var commenter = $(this).data("comment_author");
        var parent_div = $(this).parent().parent().parent();
        var the_reply_form = parent_div.find("form[name='thread_reply_form']");
        var comment_id = $(this).data("comment_id");

        the_reply_form.attr("data-replying_to_user", commenter);
        the_reply_form.attr("data-parent_comment_id", comment_id);

        the_reply_form.siblings("textarea").attr("placeholder", "Replying to " + commenter);
        //console.log(the_reply_form.data("thread_id"));

      })

    },

    add_topic_form: function() {

      if ($( "#accordion_qa_forum_add_topic" ).length === 0)
        return;

        var the_thread_id = 0;

        var publish_stat = $( "#accordion_qa_forum_add_topic" ).data("publish");

        var html_reply = "";

          html_reply += '<div data-thread_id="'+the_thread_id+'" data-common_sub_class="accordion_qa_forum_sub_reply" data-unique_tag="accordion_qa_forum_sub_reply_'+the_thread_id+'">';

          html_reply += "<div data-comment_author=''>";
          html_reply += "<div class='status'></div>";

          html_reply += "<strong><em>Ask question:</strong></em><br>";

          html_reply += '<br>  Title: <br><input type="text" placeholder="Title" name="question_title"><br><br>Detail:';

          html_reply += '<br><textarea rows="4" cols="50" name="comment" placeholder="Type your question!" form="thread_reply_form"></textarea>';

          html_reply += '<br><br><form data-publish="'+publish_stat+'" data-thread_id="'+the_thread_id+'" name="thread_ask_form"><input type="submit" value="Ask!">';

          html_reply += '</form>';


          html_reply += "</div>";


        html_reply += '</div>';


        $( "#accordion_qa_forum_add_topic" ).html(html_reply);

    },

    topic_submit_action: function() {

      $(document).on('submit', "form[name='thread_ask_form']", function(evt) {

        evt.preventDefault();

        var reply_val = $(this).siblings("textarea").val();
        var reply_title = $(this).siblings('input[name="question_title"]').val();

        var thread_id = $(this).data("thread_id");

        var publish_stat = $(this).data("publish");

        //console.log(publish_stat);


        var data = {
          'action' : 'adding_forum_thread',
          'post_title' : reply_title,
          'post_content' : reply_val,
          'publish_stat' : publish_stat
          };
        //
        var the_form_el = $(this);

        jQuery.post(plugin_data.ajax_url, data, function(response) {

            if(response)
              the_form_el.siblings(".status").html("<span style='color: green'>"+response+"!</span>");
            else
              the_form_el.siblings(".status").html("<span style='color: red'>Failed!</span>");

              location.reload();
         });


      })


    },

    load_child_comments: function() {

      $('.parent_comment').each(function(i, pComment) {

        var postID = $(this).data("post_id");
        var commentID = $(this).data("comment_id");


        var data = {
          'action' : 'request_forum_reply_comments',
          'postID' : postID,
          'commentID' : commentID

        };


        // //'post_id' : $(this).data('topic_id')
        var the_thread = $(this);
        var the_thread_id = $(this).data('topic_id');
        var the_thread_title = $(this).data('title');

        jQuery.post(plugin_data.ajax_url, data, function(response) {

          response = JSON.parse(response);

          if (response.length === 0)
            return;


            console.log(response);

            $.each(response, function(i, content) {

              console.log(content.comment_content);

                var html = "";

                html += "<div class='child_comment' data-stat='child_comment' >";
                html += "<strong><em>"+content.comment_author+": </strong></em><br>";
                html += content.comment_content
                html += '';
                html += '<span class="comment_reply"><span class="comment_time">'+content.comment_date+'</span></span>';

                html += "</div>";

              var main_data = the_thread.html();
              the_thread.html(main_data+html);



            })


         })


      })

    }


  }

  forum_actions.init();

})
