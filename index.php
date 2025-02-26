<?php include "connection.php"; ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculator App | Diandra</title>
    <link rel="icon" type="image/svg+xml" href="assets/icon.svg">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body class="flex flex-col items-center justify-center min-h-screen bg-[#2D2A37]">

    <div class="container flex flex-col border border-[#7F45E2] w-[400px] p-6 box-border
        gap-6 rounded-md relative
    
    ">

        <div class="result-area
            flex flex-col gap-2 items-end relative
        ">
            <p id="question" class="text-white font-regular text-md px-2 opacity-60"></p>
            <h1 id="result-area" class="text-5xl font-bold text-white px-2 max-w-[350px] truncate">0</h1>

            <img src="assets/history.png" alt="image" id="toggleHistoryBtn" class="absolute top-0 left-0"/>
        </div>

        <div class="grid grid-cols-4 gap-2 ">
                    <?php
                        $buttons = [
                            ["label" => "7", "type" => "number"], ["label" => "8", "type" => "number"], ["label" => "9", "type" => "number"], ["label" => "/", "type" => "symbol"],
                            ["label" => "4", "type" => "number"], ["label" => "5", "type" => "number"], ["label" => "6", "type" => "number"], ["label" => "x", "type" => "symbol"],
                            ["label" => "1", "type" => "number"], ["label" => "2", "type" => "number"], ["label" => "3", "type" => "number"], ["label" => "-", "type" => "symbol"],
                            ["label" => "C", "type" => "action"], ["label" => "0", "type" => "number"], ["label" => "=", "type" => "action"], ["label" => "+", "type" => "symbol"],
                            ["label" => "00", "type" => "number"], ["label" => "Del", "type" => "delete"], ["label" => "^", "type" => "symbol"], ["label" => ".", "type" => "symbol"]
                        ];

                    
                        foreach ($buttons as $btn) {
                            $baseStyle = "w-[80px] h-[80px] font-bold rounded text-[#EBEBEB] bg-[#2D2A37] 
                                          border border-white text-2xl border rounded-[50%] hover:bg-white";
                        
                            // Tambahkan class khusus berdasarkan tipe tombol
                            $extraStyle = "";
                            if ($btn["type"] === "number") {
                                $extraStyle = " hover:text-[#7F45E2]";  // Warna hover khusus untuk angka
                            } elseif ($btn["type"] === "symbol") {
                                $extraStyle = "bg-[#462878] border-none hover:bg-white hover:text-[#462878]";  // Warna simbol berbeda
                            } elseif ($btn["type"] === "action") {
                                $extraStyle = " text-[#E63946] hover:text-[#E63946]";  // Warna merah untuk aksi
                            }
                        
                            echo "
                                <button type='button' 
                                    onclick='pressButton(\"{$btn["label"]}\")' 
                                    class='$baseStyle $extraStyle'
                                >
                                    {$btn["label"]}
                                </button>
                            ";
                        }
                        ?>
                        
        </div>

        <div id="historyArea" class="history-area w-[240px] h-[100%] border border-[#7F45E2]
        absolute top-0 bottom-0 left-[-2600px] p-3 rounded-sm
        flex flex-col gap-2
        "
        >
            <h1 class="text-md text-white">History</h1>

            <div id="historyContainer" class="w-full h-full max-h-[100%] overflow-y-auto scrollbar-hidden"
            >
            <?php
                require 'connection.php'; // File koneksi database

                $query = "SELECT id, expression, result FROM histories ORDER BY createdAt DESC";
                $result = $conn->query($query);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $formattedExpression = $row['expression'];  // Ambil ekspresi langsung
                        $resultValue = $row['result']; // Ambil hasil
                        $itemId = $row['id'];


                        // Tampilkan dalam HTML
                        echo "
                            <div class='flex flex-col justify-between w-full bg-transparent hover:bg-[#3B3749] p-2 relative group'>
                                <h1 class='text-sm text-white opacity-60'>$formattedExpression</h1>
                                <h1 class='text-2xl text-white font-bold leading-none'>$resultValue</h1>

                                <h1 class='absolute right-2 top-1/2 -translate-y-1/2 text-transparent hover:text-white cursor-pointer transition'
                                    onclick='deleteItem(this, $itemId)'>X</h1>
                            </div>
                        ";
                    }
                } 

                $conn->close();
                ?>

            </div>

        </div>


    </div>

    <script>

        const toggleHistoryBtn = document.getElementById("toggleHistoryBtn");
        const historyArea = document.getElementById("historyArea");

        let isOpen = false;

        toggleHistoryBtn.addEventListener("click", () => {
            isOpen = !isOpen;
            historyArea.style.left = isOpen ? "-260px" : "-2600px";
        });

        let currentInput = ""; // Menyimpan input user
        let questionInput = ""
            
        function pressButton(value) {
            const resultText = document.getElementById("result-area");
            const questionText = document.getElementById("question");
            if (value === "C") {
                currentInput = ""; // Reset input
                questionInput = ""

            } else if (value === "Del") {
                currentInput = currentInput.slice(0, -1)

            } else if (value === "=") {
                currentInput = currentInput
                    .replace(/x/g, "*")   // Ubah 'x' ke '*'
                    .replace(/\^/g, "**") // Ubah '^' ke '**' untuk JavaScript
                    .replace(/√(\d+)/g, "Math.sqrt($1)"); // Ubah '√' ke 'Math.sqrt()'
                try {
                    questionInput = currentInput
                    console.log("calculating..." + currentInput)
                    fetch("process.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: `expression=${encodeURIComponent(currentInput)}`
                    })
                    .then((res) => res.json())
                    .then((res) => {
                        console.log(res)
                        if (res.status == 'success') {
                            currentInput = questionInput.toString();
                            console.log({questionInput})
                            let expression = questionInput.replace(/x/g, "*");  // Replace "X" dengan "*"
                            currentInput = eval(expression); // Hitung hasil

                            // Buat elemen history baru
                            const historyContainer = document.getElementById("historyContainer");
                            const newHistoryItem = document.createElement("div");
                            newHistoryItem.classList.add("flex", "flex-col", "justify-between", "w-full", "bg-transparent", "hover:bg-[#3B3749]", "p-2", "relative",);
                            newHistoryItem.innerHTML = `
                                <h1 class='text-sm text-white opacity-60'>${expression}</h1>
                                <h1 class='text-2xl text-white font-bold leading-none'>${res.result}</h1>

                                <h1 class='absolute right-2 top-1/2 -translate-y-1/2 text-transparent hover:text-white cursor-pointer transition'
                                    onclick='deleteItem(this, ${res.id})'>X</h1>
                            `;

                            // Tambahkan ke atas history
                            historyContainer.prepend(newHistoryItem);
                        } else {
                            console.error("Gagal menyimpan ke database:", res.message);
                        }
                    })
                    // .then((data) => console.log("Response from server : " + data))
                    .catch((err) => console.log(err))

                    let expression = currentInput.replace(/x/g, "*");  // Replace "X" symbol to "*"

                    currentInput = eval(expression); // Hitung hasil

                } catch {
                    currentInput = "Error"; // Jika ada kesalahan
                    // currentInput = "expression : " + expression; // Jika ada kesalahan=
                }
            } else {
                currentInput = currentInput + value; // Tambahkan angka/operator ke input
            }

            questionText.textContent = questionInput || ""
            resultText.textContent = currentInput || "0"; // Tampilkan hasil
        }
        
        function deleteItem(element, itemId) {

            fetch("delete.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id: itemId }),
            })
            .then(response => response.json())
            .then(data => {
            if (data.status === "success") {
                element.parentElement.remove(); // Hapus elemen dari UI
            } else {
                alert("Gagal menghapus item!");
            }
            })
            .catch(error => console.error("Error:", error));
        }

    </script>

</body>
</html>
