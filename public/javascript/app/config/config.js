var App = Ember.Application.create({
	rootElement: "#vcuApp"
});

App.Router.map(function(){
	this.route('fee');
});


App.apiURL = BASE_URL;

