@include('includes.head')
@include('includes.user-header')

<!-- PAGE CONTAINER-->
<div class="page-container">

    @include('includes.user-top')

    <!-- MAIN CONTENT-->
        <div class="main-content">

            <div class="section__content section__content--p30">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <section>
                                <h3 class="sumb--title">Files</h3>
                            </section>
               
                            @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                <strong>{{ $message }}</strong>
                            </div>
                          @endif
                          @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                      <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                          @endif

                            <div class="container mt-12">
                                <h3 class="text-center mb-12">Upload File</h3>
                                <form action="{{route('store')}}" method="POST" enctype="multipart/form-data">                                  
                                    @csrf
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input class="form-control" type="file" name="file" placeholder="Choose file" id="file">                                        
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <button type="submit" name="submit" class="btn btn-primary">
                                            Upload Files
                                        </button>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                    <br>
            <section>
                <h4 class="sumb--title2">My Transactions</h4>        
                <div class="row">
                    <div class="col-xl-12">
                        <div class="sumb--recentlogdements sumb--putShadowbox">
                            <div class="table-responsive">
                                <table>
                                    <thead>
                                        <tr>
                                            <th style="border-top-left-radius: 7px;">id</th>
                                            <th>Name</th>
                                            <th>File type</th>
                                            <th>Filesize</th>
                                            <th>Date Created</th>
                                            <th class="sumb--recentlogdements__actions" style="border-top-right-radius: 7px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    @if (empty($doclist['total']))
                                    <tr>
                                        <td colspan="8" style="padding: 30px 15px; text-align:center;">No Data At This time.</td>
                                    </tr>
                                    @else
                                        @foreach ($doclist['data'] as $doclists)

                                        <tr>
                                            <td>{{$doclists['id']}}</td>                                           
                                            <td>{{$doclists['name']}}</td>
                                            <td>{{$doclists['extensionname']}}</td>
                                            <td>{{ number_format($doclists['filesize'])." "."kb" }}</td>                                                            
                                            <td>{{ date('Y-m-d', strtotime($doclists['created_at'])) }}</td>
                                            <td class="sumb--recentlogdements__actions">
                                                @csrf
                                                <a href="/docview/?id={{$doclists['id']}}"><i class="fa-solid fa-folder-open""></i></a>                                                                                        
                                                <a href="javascript:void(0)" onclick="viewDocument('<?php echo '/docview/?id='.$doclists['id'];?>')" ><i class="fa-solid fa-file"></i></a>
                                                <a href="/doc-edit/?id={{$doclists['id']}}"><i class="fa-solid fa-edit"></i></a>
                                            
                                                <form action="/destroy/?id={{$doclists['id']}}" method="post" style="display: inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                        <button class="doc-btn" onclick="return confirm('Delete this item?')" type="submit" ><i class="fa-solid fa-trash"></i></button>
                                                </form>

                                                <a href="/downloadfile/?id={{$doclists['id']}}"><i class="fa-solid fa-file-download"></i></a>
                                            </td>                                        
                                        </tr>

                                    @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        {{-- <div class="d-flex">                            
                            {{ $doclist->links() }}
                        </div>                           --}}

                        <table>
                            <tr class="sumb--recentlogdements__pagination">
                                <td colspan="8">
                                    <!-- table pagination -->
                                    <div class="btn-group" role="group" aria-label="Basic example">

                                        <a href="{{ empty($paging['first']) ? 'javascript:void(0)' : $paging['first'] }}" type="button" class="btn btn-outline-secondary {{ empty($paging['first']) ? 'disabled' : '' }}"><i class="fas fa-angle-double-left"></i></a>
                                        <a href="{{ empty($paging['prev']) ? 'javascript:void(0)' : $paging['prev'] }}" type="button" class="btn btn-outline-secondary {{ empty($paging['prev']) ? 'disabled' : '' }}" ><i class="fas fa-angle-left"></i></a>
                                        <a href="javascript:void(0)" type="button" class="btn btn-outline-secondary" >Page {{$paging['now']}}</a>
                                        <a href="{{ empty($paging['next']) ? 'javascript:void(0)' : $paging['next'] }}" type="button" class="btn btn-outline-secondary {{ empty($paging['next']) ? 'disabled' : '' }}" ><i class="fas fa-angle-right"></i></a>
                                        <a href="{{ empty($paging['last']) ? 'javascript:void(0)' : $paging['last'] }}" type="button" class="btn btn-outline-secondary {{ empty($paging['last']) ? 'disabled' : '' }}"><i class="fas fa-angle-double-right"></i></a>

                                        <div class="btn-group" role="group">
                                            <button id="btnGroupDrop1" type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                Display: {{$doclist['per_page']}} Items
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                                <a class="dropdown-item" href="{{ $paging['starpage'].'?page=1&ipp=1' }}">1 Item</a>
                                                <a class="dropdown-item" href="{{ $paging['starpage'].'?page=1&ipp=5' }}">5 Items</a>
                                                <a class="dropdown-item" href="{{ $paging['starpage'].'?page=1&ipp=10' }}">10 Items</a>
                                                <a class="dropdown-item" href="{{ $paging['starpage'].'?page=1&ipp=25' }}">25 Items</a>
                                                <a class="dropdown-item" href="{{ $paging['starpage'].'?page=1&ipp=50' }}">50 Items</a>
                                                <a class="dropdown-item" href="{{ $paging['starpage'].'?page=1&ipp=100' }}">100 Items</a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>

                    </div>
                </div>
            </section>                          
                     

                </div>
            </div>
        </div>
    <!-- END MAIN CONTENT-->
</div>

<!-- END PAGE CONTAINER-->
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
                <iframe id="exampleModaldata" height="100%" width="100%"></iframe>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

            </div>
        </div>
        </div>
    </div>

    <script language="JavaScript" type="text/javascript">    
        function viewDocument(fileurl) {
            $('#exampleModaldata').attr("src", fileurl);
            $('#exampleModal').modal('show');
        }
    </script>

@include('includes.footer')

</body>

</html>
<!-- end document-->