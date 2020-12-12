// start basic components
$(document).ready(function() {
	$('.tabs').tabs();
	$('.modal').modal();
});

// change your province
function setProvince(province) {
	apretaste.send({
		command: 'PERFIL UPDATE',
		redirect: false,
		data: {province: province},
		callback: {name: 'openSantaCallback'}
	});
}

// open Santa Callback
function openSantaCallback(province) {
	apretaste.send({command: 'SANTA'});
}

// get a random gift icon
function getGiftIcon() {
	var gifts = ['gift','gifts','archive','shopping-bag'];
	return gifts[Math.floor(Math.random() * gifts.length)];
}

// pick the gift from the tree
function pickGift(id) {
	// remove present
	$('.gift').slideUp('fast');

	// accept present
	apretaste.send({
		command: 'SANTA REGALO',
		data: {'id': id},
		redirect: false
	});

	// show message
	$('#message').html('¡Espero que te guste mi regalo! La Navidad siempre me llena de alegría.');
}
