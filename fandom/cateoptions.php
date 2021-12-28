<!DOCTYPE html>
<html><body>
<select id = "hi">
                    <option value="Guide">Guide</option>
                    <option value="Documentation">Documentation</option>
                    <option value="Manual">Manual</option>
</select>

</body></html>
<script>
select = document.getElementById("hi");
var arr = [];
for (let option of select){
    let assoc = {
        "name" : option.innerHTML,
        "value" : option.value
    };
    arr.push(assoc);
}

console.log(JSON.stringify(arr));

</script>