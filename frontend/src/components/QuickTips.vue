<template>
    <div>
        <div id="quicktips">

            <!-- APP CONTENT -->
            <div class="container">
                <span class="project-title">
                    Quick Tips
                </span>
                <div>&nbsp;</div>

                <div class="projectslist"> 

                    <div class="project col">
                        <div class="arrow-handle">
                            <img src="../assets/images/arrowup.svg" class="arrowup">
                            <img src="../assets/images/arrowdown.svg" class="arrowdown">
                        </div>
                        <div class="project-name">
                            The Basics
                        </div>
                        <div class="separator"></div>
                        <div class="full-width col conv-builder info" >
                            <div class="row list-icons">
                                <div class="item">
                                    <a href="JavaScript:;" data-toggle="modal" data-target="#myModalProject"><figure class="project"></figure>
                                        <p>Projects</p>
                                    </a>
                                </div>
                                <div class="item">
                                    <a href="JavaScript:;" data-toggle="modal" data-target="#myModalWorkspace"><figure class="workspace"></figure>
                                        <p>Workspace</p>
                                    </a>
                                </div>
                                <div class="item">
                                    <a href="JavaScript:;" data-toggle="modal" data-target="#myModalFlows"><figure class="flows"></figure>
                                        <p>Flows</p>
                                    </a>
                                </div>
                                <div class="item">
                                    <a href="JavaScript:;" data-toggle="modal" data-target="#myModalPaths"><figure class="paths"></figure>
                                        <p>Paths</p>
                                    </a>
                                </div>
                                <div class="item">
                                    <a href="JavaScript:;" data-toggle="modal" data-target="#myModalNodes"><figure class="nodes"></figure>
                                        <p>Nodes</p>
                                    </a>
                                </div>                                           
                            </div>
                        </div> 
                    </div>

                    <div class="project col">
                        <div class="arrow-handle">
                            <img src="../assets/images/arrowup.svg" class="arrowup">
                            <img src="../assets/images/arrowdown.svg" class="arrowdown">
                        </div>
                        <div class="project-name">
                            Conversation Components
                        </div>
                        <div class="separator"></div>
                        <div class="full-width col conv-builder info" >
                            <div class="row list-icons">
                                <div class="item">
                                    <a href="JavaScript:;" data-toggle="modal" data-target="#myModalIntents"><figure class="intents"></figure>
                                        <p>Intents</p>
                                    </a>
                                </div>
                                <div class="item">
                                    <a href="JavaScript:;" data-toggle="modal" data-target="#myModalBotOutput"><figure class="botoutput"></figure>
                                        <p>Bot Output</p>
                                    </a>
                                </div>
                                <div class="item">
                                    <a href="JavaScript:;" data-toggle="modal" data-target="#myModalUserinput"><figure class="userinput"></figure>
                                        <p>User Input</p>
                                    </a>
                                </div>
                                <div class="item">
                                    <a href="JavaScript:;" data-toggle="modal" data-target="#myModalConnectors"><figure class="connectors"></figure>
                                        <p>Connectors</p>
                                    </a>
                                </div>                                         
                            </div>                                          
                        </div>
                    </div>  

                    <div class="project col advanced">
                        <div class="arrow-handle">
                            <img src="../assets/images/arrowup.svg" class="arrowup">
                            <img src="../assets/images/arrowdown.svg" class="arrowdown">
                        </div>
                        <div class="project-name">
                            Advanced Topics
                        </div>
                        <div class="separator"></div>
                        <div class="full-width col conv-builder info">

                            <div class="col item">
                                <div class="ItemTop">
                                    <div class="arrow-handle">
                                        <img src="../assets/images/arrowup.svg" class="arrowup">
                                        <img src="../assets/images/arrowdown.svg" class="arrowdown">
                                    </div>
                                    <div class="project-name-item">
                                        Bot Output
                                    </div>
                                </div>
                                <div class="full-width-item col conv-builder info-child">
                                    <div class="title">Remember User Data</div>
                                    <blockquote>
                                        <p>This can be used to save a user’s input for later use. The bot is able to reuse stored data in outputs.</p>
                                    </blockquote>
                                    <div class="title">Variables</div>
                                    <blockquote>
                                        <p>If you have saved user input to a variable using the “Remember User Data” function you can use that variable in conversation. For example, if you’ve saved the user’s name as userName, the bot could output the name like so: {userName}.<br><em>Example:</em> “Hi, {userName}, nice to see you again!”</p>
                                    </blockquote>
                                    <div class="title">Media</div>
                                    <blockquote>
                                        <p>A bot can output audio, image, or video. It can also output text along with media.</p>
                                    </blockquote>
                                </div>
                            </div>

                            <div class="col item">
                                <div class="ItemTop">
                                    <div class="arrow-handle">
                                        <img src="../assets/images/arrowup.svg" class="arrowup">
                                        <img src="../assets/images/arrowdown.svg" class="arrowdown">
                                    </div>
                                    <div class="project-name-item">
                                        User Input
                                    </div>
                                </div>
                                <div class="full-width-item col conv-builder info-child">
                                    <div class="title">Wildcards</div>
                                    <blockquote>
                                        <p>A wildcard can be used to account for ANY input or as PART OF the input. It is represented by an (*).</p>
                                        <p><em>Example:</em> “do * I” would match for both “Do you love me?” and “Do you want to go to the roller derby with me and my mom?”</p>
                                        <p>There is no need to put wildcards before or after a word or phrase you want to match. Example: “* tacos *” is no different that “tacos” so the two * are unnecessary. </p>
                                    </blockquote>
                                    <div class="title">Sentiment</div>
                                    <blockquote>
                                        <p>Sentiment analysis can be used to trigger a specific path.</p>
                                        <p><em>Example:</em> if a user is showing very negative sentiment then the bot can escalate the interaction and put them in touch with a person.</p>
                                    </blockquote>
                                    <div class="title">Conditionals</div>
                                    <blockquote>
                                        <p>Conditionals are used to specify how a user can move through the flow in addition to the user inputs. Conditionals are “if this then that” statements.</p>
                                        <p><em>Example:</em> If the user’s birthday is today’s date send a “Happy Birthday” message.</p>
                                    </blockquote>
                                    <div class="title">Concepts</div>
                                    <blockquote>
                                        <p>There are built in concepts like “everything equivalent to yes” and “all variations of no” that can be called using ~yes or ~no respectively.</p>
                                        <p><em>Examples:</em> ~howzit: phrases that mean “How are you?” or “How’s it going?"</p>
                                        <blockquote>
                                            <p>~emogoodbye: Phrases that mean “Goodbye.”</p>
                                            <p>~emohello: Phrases that mean “Hello.”</p>
                                        </blockquote>
                                    </blockquote>
                                </div>
                            </div>


                        </div>
                    </div>                                 

                </div>
            </div>

            <div class="modal show" id="myModalProject"  tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <h2>Projects</h2>
                            <p>Every project is a unique bot.</p>
                            <p>You can create additional bots by adding new projects.</p>
                        </div>
                        <div class="modal-footer">
                            <span></span>
                            <a href="JavaScript:;" data-toggle="modal" data-target="#myModalWorkspace">Workspace &gt;</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal show" id="myModalWorkspace"  tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <h2>Workspace</h2>
                            <p>The workspace is the area where you build the conversation.</p>
                            <p>Tabs are used to show different flows.</p>
                        </div>
                        <div class="modal-footer">
                            <a href="JavaScript:;" data-toggle="modal" data-target="#myModalProject">&lt; Projects</a>
                            <a href="JavaScript:;" data-toggle="modal" data-target="#myModalFlows">Flows &gt;</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal show" id="myModalFlows"  tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <h2>Flows</h2>
                            <p>Each individual flow represents a different part of the conversation.</p>
                            <p>Flows can be linked together with connectors.</p>
                        </div>
                        <div class="modal-footer">
                            <a href="JavaScript:;" data-toggle="modal" data-target="#myModalWorkspace">&lt; Workspace</a>
                            <a href="JavaScript:;" data-toggle="modal" data-target="#myModalPaths">Paths &gt;</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal show" id="myModalPaths"  tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <h2>Paths</h2>
                            <p>Each path is one way that a user can proceed through the conversation.</p>
                            <p>The user input determines which route the user takes through the flow.</p>
                        </div>
                        <div class="modal-footer">
                            <a href="JavaScript:;" data-toggle="modal" data-target="#myModalFlows">&lt; Flows</a>
                            <a href="JavaScript:;" data-toggle="modal" data-target="#myModalNodes">Nodes &gt;</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal show" id="myModalNodes"  tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <h2>Nodes</h2>
                            <p>Each node on a path is either a Bot Output or a User Input.</p>
                            <p>A node is made up of all the output/input variants or the media output.</p>
                        </div>
                        <div class="modal-footer">
                            <a href="JavaScript:;" data-toggle="modal" data-target="#myModalPaths">&lt; Paths</a>
                            <span></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal show" id="myModalIntents"  tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <h2>Intents</h2>
                            <p>Each topic the bot has a response for is called an intent.</p>
                            <p>Intents are top level user input nodes. If the bot cannot match a user input at a lower level in the flow it will attempt to match an Intent.</p>
                        </div>
                        <div class="modal-footer">
                            <span></span>
                            <a href="JavaScript:;" data-toggle="modal" data-target="#myModalBotOutput">Bot Output &gt;</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal show" id="myModalBotOutput"  tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <h2>Bot Output</h2>
                            <p>The output is what the bot will send, say, or do. The default output is text and can include multiple variants, from which one output will be selected.</p>
                        </div>
                        <div class="modal-footer">
                            <a href="JavaScript:;" data-toggle="modal" data-target="#myModalIntents">&lt; Intents</a>
                            <a href="JavaScript:;" data-toggle="modal" data-target="#myModalUserinput">User Input &gt;</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal show" id="myModalUserinput"  tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <h2>User Input</h2>
                            <p>User input is what the bot is listening for.</p>
                            <p>It attempts to match user input to a specific path.</p>
                        </div>
                        <div class="modal-footer">
                            <a href="JavaScript:;" data-toggle="modal" data-target="#myModalBotOutput">&lt; Bot Output</a>
                            <a href="JavaScript:;" data-toggle="modal" data-target="#myModalConnectors">Connectors &gt;</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal show" id="myModalConnectors"  tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <h2>Connectors</h2>
                            <p>A connector brings together a path and another path in the same flow or connects to the beginning of a different flow.</p>
                        </div>
                        <div class="modal-footer">
                            <a href="JavaScript:;" data-toggle="modal" data-target="#myModalUserinput">&lt; User Input</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</template>

<script>
    /**
     * Quick tips page
     * 
     */

    export default {
        name: 'QuickTips',

        mounted() {
            $('.modal').on('show.bs.modal', function (event) {
                $('.modal').modal('hide');
            });

            $('.info-child').hide();
            $('.projectslist .arrow-handle').find('.arrowdown').show();
            $('.projectslist .arrow-handle').find('.arrowup').hide();

            $('.projectslist .project-name').on('click', function () {
                if ($(this).hasClass('show')) {
                    $(this).removeClass('show');
                    $(this).siblings('.arrow-handle').find('.arrowdown').show();
                    $(this).siblings('.arrow-handle').find('.arrowup').hide();
                    $(this).siblings('.info').slideUp();
                } else {
                    $('.projectslist .arrow-handle').find('.arrowdown').show();
                    $('.projectslist .arrow-handle').find('.arrowup').hide();
                    $('.projectslist .project-name').removeClass('show');
                    $('.projectslist .info').slideUp();
                    $(this).addClass('show').siblings('.info').slideDown();
                    $(this).siblings('.arrow-handle').find('.arrowdown').hide();
                    $(this).siblings('.arrow-handle').find('.arrowup').show();
                }
            });


            $('.ItemTop').addClass('closed');

            $('.projectslist .project-name-item').on('click', function () {
                if ($(this).hasClass('show')) {
                    $('.ItemTop').addClass('closed');
                    $('.projectslist .info-child').slideUp();
                    $('.projectslist .project-name-item').removeClass('show');
                    $(this).removeClass('show').parent().siblings('.info-child').slideUp();
                    $(this).siblings('.arrow-handle').find('.arrowdown').show();
                    $(this).siblings('.arrow-handle').find('.arrowup').hide();
                } else {
                    $('.ItemTop').addClass('closed');
                    $(this).parent().removeClass('closed');
                    $('.projectslist .arrow-handle').find('.arrowdown').show();
                    $('.projectslist .arrow-handle').find('.arrowup').hide();
                    $('.projectslist .project-name-item').removeClass('show');
                    $('.projectslist .info-child').slideUp();
                    $(this).addClass('show').parent().siblings('.info-child').slideDown();
                    $(this).siblings('.arrow-handle').find('.arrowdown').hide();
                    $(this).siblings('.arrow-handle').find('.arrowup').show();
                }
            });

            $('.projectslist .info').hide();
            $('.projectslist .project-name').eq(0).addClass('show');
            $('.projectslist .project-name').eq(0).siblings('.info').show();
            $('.projectslist .project-name').eq(0).siblings('.arrow-handle').find('.arrowdown').hide();
            $('.projectslist .project-name').eq(0).siblings('.arrow-handle').find('.arrowup').show();



        }
    }
</script>

