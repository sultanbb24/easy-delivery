jQuery(document).ready(function($){
	
	let activeTab = localStorage.getItem('activeEdpTab');
	if(activeTab){
		$(".edp-tab").removeClass('nav-tab-active');
		$('.edp-tab[data-tab="'+ activeTab +'"]').addClass('nav-tab-active');
		$('.tab-content').hide();
		$("#tab-" + activeTab).show();
	}
	$(".edp-tab").on("click", function(e){
		e.preventDefault();
		let tabId = $(this).data("tab");
		localStorage.setItem('activeEdpTab', tabId);
		$(".edp-tab").removeClass("nav-tab-active");
		$(this).addClass("nav-tab-active");
		$('.tab-content').hide();
		$("#tab-" + tabId).show();

	});
	$(".edp-newStore").click(function(){
		$("#edp-store-form")[0].reset();
		$("#edpStore-id").val(0);
		$("#modal-title").text('Add New Store');
		$(".edp-store-modal").show();
	});
	$(".edp-modal-close, .edp-modal-closebtn").click(function(){
		$(".edp-store-modal").hide();
	});
	$(window).on("click", function(e){
        if($(e.target).is(".edp-store-modal")){
            $(".edp-store-modal").hide();
        }
    });
	//Loader Helper
	function loadLoader(text){
		$(".edp-store-modal").hide();
		$("#store_loader").css("display", "flex");
		$("#store_loader").show();
		$("#loader_text").text(text || "Processing...");
	}
	function hideLoader(){
		$("#store_loader").hide();
	}
	//Save Store Details
	$("#edp-store-form").on("submit", function(e){
		e.preventDefault();
		loadLoader("Saving Store");
		const formData = $(this).serializeArray();
		$.ajax({
			url: edp_obj.ajax_url,
			type: 'POST',
			data:{
				action: 'save_edp_store_data',
				nonce: edp_obj.nonce,
				data: formData
			},
			success: function(response){
				loadLoader(response.data.message);
				if(response.success){
					location.reload();
				}else{
					alert('Error: ' + response.data);
				}
			}
		});
	});
	//Edit Store
	$(".edit_store").on("click", function(){
		let storeId = $(this).data("id");
		loadLoader("Loading Store Details");
		$.ajax({
			url: edp_obj.ajax_url,
			type: 'POST',
			data: {
				action: "edit_store_details",
				store_id: storeId,
				security: edp_obj.nonce
			},
			success: function(response){
				hideLoader();
				if(response.success){
					let store = response.data;
					$("#modal-title").text('Edit Store Details');
					$("#edpStore-id").val(store.id);
					$("#store_name").val(store.store_name);
					$("#store_address").val(store.store_address);
					$("#pickup_time").val(store.pickup_time);
					$("#delivery_charge").val(store.delivery_charge);

					$("input[name=\"opening_days[]\"]").prop("checked", false);
					if(store.opening_days){
						store.opening_days.forEach(function(day){
							$("input[name=\"opening_days[]\"][value=\"" + day + "\"]").prop("checked", true);
						});
					}
					$(".edp-store-modal").show();
				}
			}
		});
	});
	//Delete Store Operation
	$(".delete_store").on("click", function(){
		if( ! confirm("Are you sure want to delete this Store?")){
			return;
		}
		let storeId = $(this).data("id");
		loadLoader("Deleting Store...");
		$.ajax({
			url: edp_obj.ajax_url,
			type: "POST",
			data: {
				action: "delete_store_details",
				store_id: storeId,
				nonce:edp_obj.nonce
			},
			success: function(response){
				if(response.success){
					loadLoader(response.data);
					setTimeout(function(){
						location.reload();
					}, 800);
				}
			}
		});

	});
});