jQuery( document ).ready(function($) {
	//generate_w3vx_vote_buttons needs to be a global variable.
	//https://stackoverflow.com/a/2223341
	window.generate_w3vx_vote_buttons = function($ids, $type){
	
		// if $type is not defined, end function
		if(typeof $type == "undefined") return;
	
		
		if($ids.constructor === Array){
			//
		} else {		// if it's not an array, make it one.
			$ids = Number($ids);//convert string to integer
			$ids = [$ids];
		}
	
		$ids = JSON.stringify($ids, null, ' ');
	
		$.ajax({
			url: w3vx_ajax.ajaxurl,
			type: "POST",
			data: {
				action: 'w3vx_get_vote_data',
				ids: $ids,
				type: $type,
			},
			dataType: "json",			
			success: function ( $results ) {
				$.each($results, function($id, $value){
					//REMINDER: the id (e.g. voteButton-1 should be something more context-specific like "commentVoteButton-1")
					// REMEMBER: We want no space between the id and the class because they belong to the same DOM element.
					
					if($value.active == "disable"){
						$("#voteButton-" + $id + "." + $value["type"] + "-vote .vote-up").addClass("disable");
						$("#voteButton-" + $id + "." + $value["type"] + "-vote .vote-down").addClass("disable");
					} else if($value.active == "up"){
						$("#voteButton-" + $id + "." + $value["type"] + "-vote .vote-up").addClass("active");
						if($("#voteButton-" + $id + "." + $value["type"] + "-vote .vote-down").hasClass("active")){
							$("#voteButton-" + $id + "." + $value["type"] + "-vote .vote-down").removeClass("active");
						}
					} else if($value.active == "down") {
						if($("#voteButton-" + $id + "." + $value["type"] + "-vote .vote-up").hasClass("active")){				
							$("#voteButton-" + $id + "." + $value["type"] + "-vote .vote-up").removeClass("active");
						}
						$("#voteButton-" + $id + "." + $value["type"] + "-vote .vote-down").addClass("active");
					}
					
					if("#"+$($value["type"] +"VoteCount-" + $id).length !== 0) {
						$("#"+$value["type"] +"VoteCount-" + $id).text($value["count"]);
					}
				});
			}
		});
	}
	
	// UPDATE VOTE
	$(document).on( 'click', '.vote-button', function() {
		if(w3vx_ajax.loggedin == 0){
			return null;
		}
		
		if(!$(this).hasClass("disable")) {
			type = $(this).data("type");

			if($(this).hasClass("active")) {
				vote = "";
				
				$(this).removeClass("active");
			} else {
				vote = $(this).data("vote");
				
				voteButtons = $("#voteButton-" + $(this).data("pid")).find(".vote-button");
				
				// remove other active button if present		
				voteButtons.each(function(index){
					voteButtons.eq(index).removeClass("active");
				});
			
				// make current button active
				$(this).addClass("active");
			}

			$.ajax({
				url: w3vx_ajax.ajaxurl,
				type: "POST",
				data: {
					action: 'w3vx_voting_handler',
					vote: vote,
					type: type,
					pid: $(this).data("pid")
				},
				dataType: "json",			
				success: function ( result ) {
					$("#"+type+"VoteCount-"+result.pid).html(result.counter);
				}
			});
		}
	});

	$ids = [];	
	$(document).find('.vote-buttons-wrapper.post-vote').each(function(){	
		$ids.push($(this).data("id"));
	});	
	generate_w3vx_vote_buttons($ids, "post");	
});
