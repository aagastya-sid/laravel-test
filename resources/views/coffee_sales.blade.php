<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('New ☕️ Sales') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form name="sale_form">
                        <div class="flex">
                            <div class="mr-4">
                                <label class="block text-sm font-medium text-gray-600">Quantity</label>
                                <input type="number" name="quantity" value="0" class="mt-1 p-2 border rounded-md w-60">
                                <p id="quantity_error" class="text-red-500 hidden"></p>
                            </div>
                            <div class="mr-4">
                                <label class="block text-sm font-medium text-gray-600">Unit Cost</label>
                                <input type="number" name="unit_cost" value="0" class="mt-1 p-2 border rounded-md w-60">
                                <p id="unit_cost_error" class="text-red-500 hidden"></p>
                            </div>
                            <div class="mr-4">
                                <label class="block text-sm font-medium text-gray-600 w-60 text-center">Selling Price</label>
                                <p id="selling_price" class="text-center text-3xl">{{ \Akaunting\Money\Money::EUR(0) }}</p>
                            </div>
                            <div>
                                <button type="button" onclick="recordSale()"
                                        class="mt-8 p-2 bg-blue-500 text-white rounded-md">
                                    Record Sale
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
{{--                <p id="error_message"></p>--}}
            </div>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-2">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <div class="container">
                        <table class="min-w-full border border-gray-300">
                            <thead>
                            <tr class="text-left">
                                <th class="py-2 px-4 border-b">Quantity</th>
                                <th class="py-2 px-4 border-b">Unit Cost</th>
                                <th class="py-2 px-4 border-b">Selling Price</th>
                            </tr>
                            </thead>
                            <tbody id="coffee-sales">
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>

    //var unique order id with timestamp
    var order_id = 'order_' + new Date().getTime();
    document.querySelector('input[name="quantity"]').addEventListener('input', function (e) {
        calculateSellingPrice();
    });

    document.querySelector('input[name="unit_cost"]').addEventListener('input', function (e) {
        calculateSellingPrice();
    });

    function calculateSellingPrice() {
        let quantity = document.querySelector('input[name="quantity"]').value;
        let unitCost = document.querySelector('input[name="unit_cost"]').value;
        if (quantity > 0 && unitCost > 0) {
            $.ajax({
                type: 'POST',
                url: '{{ route('selling.price') }}',
                data: {
                    quantity: quantity,
                    unit_cost: unitCost
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    document.querySelector('#selling_price').innerText = data.selling_price;
                },
                error: function (data) {
                    alert('An error occurred');
                }
            });
        }
    }

    function recordSale() {
        const formData = {
            quantity: document.querySelector('input[name="quantity"]').value,
            unit_cost: document.querySelector('input[name="unit_cost"]').value,
            order_id: order_id
        };

        $.ajax({
            type: 'POST',
            url: '{{ route('record.sale') }}',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (data) {
                resetForm();
                alert('Sale recorded successfully');
                getSales();
            },
            error: function (data) {
                let errors = data.responseJSON.errors;
                if (errors.quantity) {
                    document.querySelector('#quantity_error').innerText = errors.quantity[0];
                    document.querySelector('#quantity_error').classList.remove('hidden');
                }
                if (errors.unit_cost) {
                    document.querySelector('#unit_cost_error').innerText = errors.unit_cost[0];
                    document.querySelector('#unit_cost_error').classList.remove('hidden');
                }
            }
        });
    }

    function getSales() {
        const data = {
            order_id: order_id
        };
        $.ajax({
            type: 'POST',
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('get.sales') }}",
            success: function (data) {
                let sales = data;
                let salesHtml = '';
                sales.forEach(sale => {
                    salesHtml += `<tr>
                        <td class="py-2 px-4 border-b">${sale.quantity}</td>
                        <td class="py-2 px-4 border-b">${sale.unit_cost}</td>
                        <td class="py-2 px-4 border-b">${sale.selling_price}</td>
                    </tr>`;
                });
                document.querySelector('#coffee-sales').innerHTML = salesHtml;
            },
            error: function (data) {
                alert('An error occurred');
            }
        });
    }

    function resetForm() {
        document.querySelector('input[name="quantity"]').value = 0;
        document.querySelector('input[name="unit_cost"]').value = 0;
        document.querySelector('#selling_price').innerText = '';
    }


</script>
