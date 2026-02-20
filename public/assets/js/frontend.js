jQuery(document).ready(function($){
	let dateRangeRestriction = edp_general_obj.date_range ? edp_general_obj.date_range : 30;
	let disableSameDay = edp_general_obj.disable_same_day ? edp_general_obj.disable_same_day : 0;
	console.log(disableSameDay);

	//Date Picker function
	function initDatePicker(selector, allowDays){
		let startOffset = parseInt(disableSameDay) === 1 ? 1 : 0;
		const dateConfig = {
			dateFormat: "F j, Y",
			minDate: new Date().fp_incr(startOffset),
			maxDate: new Date().fp_incr(parseInt(dateRangeRestriction)),
			disableMobile: "true",
			showMonths: 1,
			enable: [
				function(date){
					const dayName = date.toLocaleDateString('en-US', {weekday: 'long'});
					if(allowDays.length > 0){
						return allowDays.indexOf(dayName) !== -1;
					}
					return true;
				}
			],
			onDayCreate: function(dObj, dStr, fp, dayElement){
				const date = dayElement.dateObj;
				const dayName = date.toLocaleDateString('en-US', {weekday: 'long'});
				const isAllowed = allowDays.length === 0 || allowDays.indexOf(dayName) !==-1;
				const isPast = date < fp.config.minDate;
				const isFuture = date > fp.config.maxDate;
				if(!isAllowed){
					dayElement.setAttribute('title', 'Store Closed');
				}else if(isPast || isFuture){
					dayElement.setAttribute('title', 'Not Available');
				}else{
					dayElement.setAttribute('title', 'Avaiable');
				}
			},
			locale: {
				firstDayOfWeek: 1
			},
			onChange: function(selectedDates, dateStr, instance){
				$(instance.element).trigger('change');
			}
		};
		if($(selector).length){
			flatpickr(selector, dateConfig)
		}
	}

	/*
	*Classic Checkout Operation
	*/
	$('#edp_checkout_order_type').on('change', function(){
		let checkoutOrderTyepe = $(this).val();
		edpCheckoutOrderType(checkoutOrderTyepe);
	});
	function edpCheckoutOrderType(checkoutOrderTyepe){
		if(checkoutOrderTyepe){
			if(checkoutOrderTyepe === 'pickup'){
				$('.edp-checkout-pickup-fields').show();
				$('.edp-checkout-delivery-fields').hide();
				//Reset Delivery Value
				$('#edp_checkout_delivery_store').val('');
				$('#edp_checkout_delivery_date').val('');
			}else{
				$('.edp-checkout-delivery-fields').show();
				$('.edp-checkout-pickup-fields').hide();
				//Reset Pickup Value
				$('edp_checkout_pickup_store').val('');
				$('edp_checkout_pickup_date').val('');
				$('edp_checkout_pickup_time').val('');
			}
		}else{
			$('.edp-checkout-delivery-fields').hide();
			$('.edp-checkout-pickup-fields').hide();
		}
	}
	$('#edp_checkout_pickup_store').on('change', function(){
		let selectedOption = $(this).find('option:selected');
		if(selectedOption.val()){
			let openingDays = JSON.parse(selectedOption.attr('data-opening-days') || '[]');
			let pickupTime = selectedOption.attr('data-pickup-time');
			let storeName = selectedOption.attr('data-store-name');
			let storeAddress = selectedOption.attr('data-store-address');
			//Reset Date field when change value
			$('#edp_checkout_pickup_date').val('');
			//Set Value 
			$('#edp_selected_pickup_store_name').val(storeName);
			$('#edp_checkout_pickup_time').val(pickupTime);
			$('#edp_selected_pickup_store_address').val(storeAddress);
			initDatePicker('#edp_checkout_pickup_date', openingDays);
		}
	});
	$('#edp_checkout_delivery_store').on('change', function(){
		let selectedOption = $(this).find('option:selected');
		if(selectedOption.val()){
			let openingDays = JSON.parse(selectedOption.attr('data-opening-days') || '[]');
			let storeName = selectedOption.attr('data-store-name');
			let deliveryCharge = selectedOption.attr('data-delivery-charge');
			//Reset Date Value
			$('#edp_checkout_delivery_date').val('');
			//set Hidden Field
			$('#edp_selected_delivery_store_name').val(storeName);
			initDatePicker('#edp_checkout_delivery_date', openingDays);
		}
	});
	$('body').on('click', '#place_order', function(e){
		let finalOrderType = $('#edp_checkout_order_type').val();
		if(finalOrderType){
			if(finalOrderType === 'pickup'){
				if( ! $('#edp_selected_pickup_store_name').val()){
					e.preventDefault();
					alert('Please Select Pickup Store');
					return false;
				}
				if( ! $('#edp_checkout_pickup_date').val()){
					e.preventDefault();
					alert('Please select Pickup Date');
					return false;
				}
			}else{
				if( ! $('#edp_selected_delivery_store_name').val()){
					e.preventDefault();
					alert('Please Select Delivery Store Name');
					return false;
				}
				if( ! $('#edp_checkout_delivery_date').val()){
					e.preventDefault();
					alert('Please select Delivery Date');
					return false;
				}
			}
		}else{
			alert('You Must Select a Order Type');
			e.preventDefault();
			return false;
		}
	});
///End jQuery
});