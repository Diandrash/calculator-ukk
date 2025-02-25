<?php include "connection.php"; ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalkulator PHP</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex flex-col items-center justify-center min-h-screen bg-[#2D2A37]">

    <div class="container flex flex-col border border-[#7F45E2] w-[400px] p-6 box-border
        gap-6 rounded-md
    
    ">

        <div class="result-area
            flex flex-col gap-2 items-end 
        ">
            <p id="question" class="text-white font-regular text-md px-2 opacity-60"></p>
            <h1 id="result-area" class="text-5xl font-bold text-white px-2">0</h1>
        </div>

        <div class="grid grid-cols-4 gap-2 ">
                    <?php
                        $buttons = [
                            ["label" => "7", "type" => "number"], ["label" => "8", "type" => "number"], ["label" => "9", "type" => "number"], ["label" => "/", "type" => "symbol"],
                            ["label" => "4", "type" => "number"], ["label" => "5", "type" => "number"], ["label" => "6", "type" => "number"], ["label" => "x", "type" => "symbol"],
                            ["label" => "1", "type" => "number"], ["label" => "2", "type" => "number"], ["label" => "3", "type" => "number"], ["label" => "-", "type" => "symbol"],
                            ["label" => "C", "type" => "action"], ["label" => "0", "type" => "number"], ["label" => "=", "type" => "action"], ["label" => "+", "type" => "symbol"]
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
    </div>

    <!-- <div class="bg-white shadow-lg rounded-lg p-6 w-96">
        <h2 class="text-xl font-bold text-center mb-4">Kalkulator</h2>
        <form action="process.php" method="POST" class="flex flex-col space-y-2">
            <input type="number" name="first_numbers" placeholder="Masukkan angka pertama" required class="border p-2 rounded">
            <select name="expression" required class="border p-2 rounded">
                <option value="+">+</option>
                <option value="-">-</option>
                <option value="*">×</option>
                <option value="/">÷</option>
            </select>
            <input type="number" name="second_numbers" placeholder="Masukkan angka kedua" required class="border p-2 rounded">
            <button type="submit" class="bg-blue-500 text-white py-2 rounded hover:bg-blue-600">Hitung</button>
        </form>
    </div> -->

    <!-- History -->
    <!-- <div class="bg-white shadow-lg rounded-lg p-6 w-96 mt-6">

        <h2 class="text-xl font-bold text-center mb-4">Riwayat Perhitungan</h2>
        <ul>
            // <?php
                // $sql = "SELECT * FROM history"; 
                // $result = $conn->query($sql);

                // if (!$result) {
                    // die("Kesalahan SQL: " . $conn->error); // Debugging SQL error
                // }

                // while ($row = $result->fetch_assoc()) {
                    // echo "ID: " . $row["id"] . " | " . $row["first_numbers"] . " " . $row["expression"] . " " . $row["second_numbers"] . " = " . $row["result"] . "<br>";
                // }
            // ?>
        </ul>
    </div> -->
    

    <script>
    let currentInput = ""; // Menyimpan input user
    let questionInput = ""

    function pressButton(value) {
        const resultText = document.getElementById("result-area");
        const questionText = document.getElementById("question");
        if (value === "C") {
            currentInput = ""; // Reset input
            questionInput = ""

        } else if (value === "=") {
            try {
                questionInput = currentInput
                console.log({currentInput})
                console.log("calculating...")
                fetch("process.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `expression=${encodeURIComponent(currentInput)}`
                })
                // .then((res) => res.json())
                .then((res) => console.log(res))
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
</script>

</body>
</html>
