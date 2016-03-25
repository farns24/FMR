$(document).ready(function() {
	//Set up menu
	$('#newProjectWrapper').hide();
	$('#searchWrapper').show();
	$('#queryForm').hide();
	
	
	
	$('#menuSearch').click(function(){
		$('#newProjectWrapper').hide();
		$('#searchWrapper').show();
		$('#queryForm').hide();
		
		$('#menuSearch').addClass('active');
		$('#menuAnalyze').removeClass('active');
		$('#newProject').removeClass('active');
	});
	$('#menuAnalyze').click(function(){
		$('#newProjectWrapper').hide();
		$('#searchWrapper').hide();
		$('#queryForm').show();
		
		$('#menuSearch').removeClass('active');
		$('#menuAnalyze').addClass('active');
		$('#newProject').removeClass('active');
	});
	$('#newProject').click(function(){
		$('#newProjectWrapper').show();
		$('#searchWrapper').hide();
		$('#queryForm').hide();
		
		$('#menuSearch').removeClass('active');
		$('#menuAnalyze').removeClass('active');
		$('#newProject').addClass('active');		
	});
	
	$('#loadingAnalysis').hide();
	//Loading in analysis
	$('.startLoader').click(function(){
		$('#loadingAnalysis').show();
		$('#loadingAnalysis').dialog();
	});
	
	
	//$('button').fadeTo('fast',.75);
	$('.hide_on_enter').toggle();
  /* $('button').mouseenter(function() {
	   $(this).fadeTo('fast',1);
   });
   $('button').mouseleave(function() {
	   $(this).fadeTo('fast',.80);
   });*/
   $('.searchBtn').click(function() {
		$('.searchLoc').remove();
		//$(this).show();
	
   });
   $('.searchBtn').mouseenter(function() {
	   $(this).fadeTo('fast',1);
   });
    $('.searchBtn').mouseleave(function() {
	   $(this).fadeTo('fast',.75);
   });
   /* generations*/
    $('.map1').hide();
	$('.map2').hide();
	$('.map3').hide();
	$('.map4').hide();
   
    $('#gen1head').mouseenter(function() {
		$('.map1').show();
		
	
   });

   
    $('#gen2head').mouseenter(function() {
		$('.map2').show();
		
	
   });   

   
    $('#gen3head').mouseenter(function() {
		$('.map3').show();
		
	
   }); 

    $('#gen4head').mouseenter(function() {
		$('.map4').show();
		
	
   });  
   
	$("#createProjectBtn").click(function(){
		 $("#createProjectDiv").load('/fmr/newProject.html');
		
		$(".hide_while_creating_projects").toggle();
	});
   $('.sortMe').find('.searchIcon').sort(function(a, b) {
    return +a.dataset.number - +b.dataset.number;
})
.appendTo(this);
   
});