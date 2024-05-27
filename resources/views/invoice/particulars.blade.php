@include('includes.headwhite')

<div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
            <table class="table" id="partstable">
                <thead>
                    <tr>
                        <th scope="col">QTY</th>
                        <th scope="col">Description</th>
                        <th scope="col">Unit Price</th>
                        <th scope="col">Amount</th>
                        <th scope="col" style="text-align: right;">Options</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">-</th>
                        <td>Computer Repair Services<br> this is a second lane<br> this is the third lane</td>
                        <td style="text-align: right;">-</td>
                        <td style="text-align: right;">$200.00</td>
                        <td style="text-align: right;">
                            <a class="btn btn-primary btn-sm" href="#" role="button"><i class="fa-regular fa-pen-to-square"></i></a>
                            <a class="btn btn-primary btn-sm" href="#" role="button"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">1</th>
                        <td>Acer Motherboard L3878</td>
                        <td style="text-align: right;">$500.00</td>
                        <td style="text-align: right;">$500.00</td>
                        <td style="text-align: right;">
                            <a class="btn btn-primary btn-sm" href="#" role="button"><i class="fa-regular fa-pen-to-square"></i></a>
                            <a class="btn btn-primary btn-sm" href="#" role="button"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">3</th>
                        <td>Acer Memory 4GB 3pcs</td>
                        <td style="text-align: right;">$100.00</td>
                        <td style="text-align: right;">$300.00</td>
                        <td style="text-align: right;">
                            <a class="btn btn-primary btn-sm" href="#" role="button"><i class="fa-regular fa-pen-to-square"></i></a>
                            <a class="btn btn-primary btn-sm" href="#" role="button"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" style="text-align: right;"><strong>Total Amount</strong></td>
                        <td style="text-align: right;"><strong>$1,000.00</strong></td>
                        <td>&nbsp;</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

        




@include('includes.footer')
</body>

</html>
<!-- end document-->