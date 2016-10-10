@extends('dashboard')
@section('content')
  <div class="box box-primary">
            <div class="box-header with-border">
    <a href="{{url('roles')}}" class="btn btn-default btn-sm active" role="button">返回</a>
    @if(session('message'))
    <p class="bg-success">{{session('message')}}</p>
    @endif    
        </div>
            <!-- /.box-header -->
            <!-- form start -->
<form method="POST" action="{{ url('roles/'.$role->id) }}">
 {!! csrf_field() !!} 
 <input type="hidden" name="_method" value="PUT">
              <div class="box-body">
                <div class="form-group">
                  <label >角色名称</label>
                  <input type="textfield" name="name" value="{{$role->name}}" class="form-control" placeholder="Enter name">
                </div>
                <div class="form-group">
                  <label >描述</label>
                  <textarea class="form-control" name="description">{{ $role->description }}</textarea>
                </div>
              <!-- /.box-body -->
<input type="hidden" value="{{$role->id}}" />
              <div class="box-footer">
                <button type="submit" class="btn btn-primary">提交</button>
              </div>
</form>
         </div>
          <!-- /.box -->
@endsection
