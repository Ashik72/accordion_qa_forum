
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


          jQuery.post(plugin_data.ajax_url, data, function(response) {

          });
