  <script>
        function addpayment(id,c=0) {

            document.getElementById("message-payment").innerHTML = '';

            document.getElementById('mod_id').value = id;
            @if(isset(request()->pos))
            var v = parseFloat(document.getElementById('grand-total').innerText);
            document.getElementById('still').value = v;
            document.getElementById('paid').max = v;
            document.getElementById('paid').value = v;
            @else
            document.getElementById('still').value = parseFloat(document.getElementById('due_' + id).textContent.replace(/,/g, "")).toFixed(2);
            document.getElementById('paid').max = parseFloat(document.getElementById('due_' + id).textContent.replace(/,/g, "")).toFixed(2);
            @endif
            var modal = document.getElementById("myModal2");
            modal.style.display = "block";
            var span = document.getElementsByClassName("close")[c];
            span.onclick = function() {
                modal.style.display = "none";
            }
        }

        function savepayment() {
            var id = document.getElementById('mod_id').value;
            var sale_id_selector = "sale_" + id
            var real_date = document.getElementById("real_date").value;
            var payment_type_id = document.getElementById("payment_type_id").value;
            var due_date = document.getElementById("due_date").value;
            var paid = document.getElementById("paid").value;
            var comment = document.getElementById("comment").value;
            if(paid > parseFloat(document.getElementById("still").value) || (paid == 0 || paid == '') ){
                if((paid == 0 || paid == '')){
                    document.getElementById("message-payment").innerHTML = '<b class="red"> {{ __("messages.paid_Cannot_be_empty") }}</b>';
                    return false;
                }
                document.getElementById("message-payment").innerHTML = '<b class="red"> {{ __("Paid Cannot be more than Due Value") }}</b>';
                return false;
            }
            //sale_  grandtotal paid
            //still
            var data = [];
            data['sale_id'] = id;
            data['real_date'] = real_date;
            data['payment_type_id'] = payment_type_id;
            data['due_date'] = due_date;
            data['paid'] = paid;
            data['comment'] = comment;
            const added_data = 'comment=' + comment + '&paid=' + paid + '&due_date=' + due_date + '&payment_type_id=' +
                payment_type_id + '&real_date=' + real_date + '&sale_id=' + id + '&_token=' + '{{ csrf_token() }}';
            fetch('/admin/paymentsales?' + added_data, {
                    method: 'POST',
                    body: JSON.stringify({
                        data
                    })
                }).then(response => response.json())
                .then(data => {
                    // You can access the response data here
                    console.log(data.message);
                    console.log(data.newdue);
                    console.log(data.newpaid);
                    console.log(data.returnedstatus);
                    var modal = document.getElementById("myModal2");
                    modal.style.display = "none";
                    console.log(id);
                    document.getElementById('ajaxmessage').innerHTML =
                        '<div class="alert alert-success toremove-beforeajax mt-4" id="message"><i class="fa fa-check-circle fa-lg"></i>' +
                        data.message + '</div>';
                    document.getElementById('paid_' + id).innerHTML = '<span class="badge bg-success">' + data.newpaid.toFixed(2) +
                        '</span>';
                    document.getElementById('due_' + id).innerHTML = '<span class="badge bg-danger">' + data.newdue.toFixed(2) +
                        '</span>';
                    document.getElementById('paymentstatus_' + id).innerHTML = '<span class="badge bg-' + data
                        .returnedstatusclass + '">' + data.returnedstatus + '</span>';


                })
                .catch(error => {
                    // handle network or server error
                });
        }
        document.addEventListener("DOMContentLoaded", function() {
            var userinfos = document.querySelectorAll(".show-payments");
            userinfos.forEach(function(userinfo) {
                userinfo.addEventListener("click", function() {
                    var id = this.getAttribute("data-id");
                    fetch("/admin/paymentsales/" + id, {
                            method: "GET",
                            headers: {
                                "Content-Type": "application/x-www-form-urlencoded"
                            }
                        })
                        .then(function(response) {
                            return response.text();
                        })
                        .then(function(text) {
                            document.querySelector(".modal-body").innerHTML = text;
                            var modal = document.getElementById("empModal");
                            modal.style.display = "block";
                        });
                });
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            var btnClose = document.querySelector(".btn-close");
            btnClose.addEventListener("click", function() {
                var modal = document.getElementById("myModal2");
                modal.style.display = "none";
                var modal = document.getElementById("empModal");
                modal.style.display = "none";
            });

        });

    </script>
