@extends('dashboard')
@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <a href="{{url($moduleRoute)}}" class="btn btn-default btn-sm active" >返回</a>
        @if(session('message'))
        <p class="bg-success">{{session('message')}}</p>
        @endif    
    </div>
<form method="POST" action="{{ url($moduleRoute.'/'.$object->id) }}" >
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<input type="hidden" name="_method" value="PUT">
    <div class="box-body">
                <div class="form-group">
                  <label >模块名</label>
                  <input type="textfield" name="name" value="{{$object->name}}" class="form-control" >
                  @if($errors->has('name'))
                    {{$errors->first('name')}}
                  @endif 
                </div>
                <div class="form-group">
                  <label >控制器</label>
                  <input type="textfield" name="controller" value="{{$object->controller}}" class="form-control" >
                  @if($errors->has('controller'))
                    {{$errors->first('controller')}}
                  @endif 
                </div>
                <div class="form-group">
                   <label>比重</label>
                   <input type="textfield" name="weight" value="{{$object->weight}}" class="form-control" >
                  @if($errors->has('weight'))
                    {{$errors->first('weight')}}
                  @endif 
                </div>
               <div class="form-group">
                 <label>生成菜单</label><br>
                 @if($object->menu == 1)
                    <input type="radio" name="menu" value="1" checked> 是
                    <input type="radio" name="menu" value="0"> 否
                @else   
                    <input type="radio" name="menu" value="1"> 是
                    <input type="radio" name="menu" value="0" checked> 否

                 @endif

                 @if($errors->has('menu'))
                    {{$errors->first('menu')}}
                  @endif 
                </div>
              <div class="form-group">
                <label>描述</label><br>
               <textarea name="description" class="form-control" >{{$object->description}}</textarea>
                </div>
              <div class="box-footer">
                <button type="submit" class="btn btn-primary">提交</button>
              </div>
</form>
         </div>
@endsection
