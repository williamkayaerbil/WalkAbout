<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Walkabout Admin</title>

   <link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}">

	<!--[if lt IE 9]>
  		<script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/r29/html5.min.js"></script>
  	<![endif]-->

    <link rel="stylesheet" href="{{asset('css/bootstrap-datetimepicker.min.css')}}">
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
		<link  rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/select2/3.5.2/select2.min.css" />
		<link  rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/select2/3.5.2/select2-bootstrap.min.css" />

    <link rel="stylesheet" href="{{asset('css/styles.css')}}">

</head>
<body>
  @include('admin.header')
  <div class="container-fluid">
    <div class="row">
     <div class="col-sm-3 col-md-2 sidebar">
  	  @include('admin.sidebar')
  	  </div>
  	  <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
         @include('partials/breadcrumb')
  	  	@yield('content')
  	  </div>
  	</div>

  </div>




<div class="modal fade" id="modal-import" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" >Import</h4>
      </div>
      <div class="modal-body">
        @yield('modal-import')
         <div class="modal-footer">
         </div>
      </div>
    </div>
  </div>
</div>





<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script charset="utf-8" src="https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.2/select2.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
<script src="//maps.googleapis.com/maps/api/js?key=AIzaSyBtik1WlRukGIeooM_iQirsnrj59gDFHV8&v=3&libraries=geometry,places"></script>

<script>
var root="{{url('/')}}";
  function readURL(input,img) {

    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $(img).attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}
  $(document).on('ready',function(){
    $('[data-toggle="tooltip"]').tooltip({
      trigger: 'hover '
    })
		$('select').select2();
    $("#remove-item").on('click',function(e){
      e.preventDefault();
        if($('input[name="delete[]"]:checked').length && confirm("Are you sure you want to delete these items?")){
          $("#form-table").submit();
        }
    });
    $('#btn-deleted').on('click',function(){

        $('#checkbox-deleted')[0].checked=!$(this).hasClass('active');
        $('.filters form').submit();
    });
  });
  Walkabout={
    getCategories: function(map_id,type,first){
      if(!map_id || !type) return;
      $.ajax({
        url: '{{url("api")}}/categories',
        type: 'GET',
        dataType: 'JSON',
        data: {map_id: map_id,type:type},
        success:function(res){
          var $el=$("#category_id");
          $el.html("");
          var html=[],
          name;
          if(!res.length){
            html.push(
              '<option value=""></option>'
              );
          }
          if(first){
            html.push(
              '<option value="'+first.id+'">'+first.name+'</option>'
              );
          }
          for(var i in res){
            name=res[i].name? res[i].name:res[i].title;
            html.push(
              '<option value="'+res[i].id+'">'+name+'</option>'
              );
          }
          $el.html(html.join(" "));
          $el.trigger('change');
          //$el=null;

        }
      });
    },
    fillSelect: function(id_element,value,model,first){
      $el=$("#"+id_element);
      if(!value) {
        $el.html("")
        if(first){
          $el.html('<option value="'+first.id+'">'+first.name+'</option>');
        }
        return;
      }
      $.ajax({
        url: '{{url("api")}}/'+model,
        type: 'GET',
        dataType: 'JSON',
        data: {parent_id: value},
        success:function(res){
          $el.html("");
          var html=[],
          name;
          if(!res.length){
            html.push(
              '<option value=""></option>'
              );
          }
          if(first){
            html.push(
              '<option value="'+first.id+'">'+first.name+'</option>'
              );
          }
          for(var i in res){
            name=res[i].name? res[i].name:res[i].title;
            html.push(
              '<option value="'+res[i].id+'">'+name+'</option>'
              );
          }
          $el.html(html.join(" "));
          $el.trigger('change');
          //$el=null;

        }
    });
    }
  };

</script>



@yield('script')
</body>
</html>
