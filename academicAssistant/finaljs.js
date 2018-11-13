function addClass() {
    var myWindow = window.open("addClass.html", "_blank", "width=1000,height=500");
}

function addRows() {
      //
      document.getElementById("reC").value = parseInt(document.getElementById("reC").value) + 1;//

      //Add row
      var i = parseInt(myTable.rows.length);

      var newTr = myTable.insertRow();
      //Add column
      var newTd0 = newTr.insertCell();
      var newTd1 = newTr.insertCell();
      var newTd2 = newTr.insertCell();

      newTd0.innerHTML = '<input type="text" id="dpDate' + i + '_dpDate' + i + '" style="width:98%;" title="Category" value="" />';
      newTd1.innerHTML = '<input type="text" id="txtR' + i + '_1" style="width:98%;" title="Weight"  value=""/>';

      newTd2.innerHTML = '<input type="submit" class="btn btn-mini btn-warning"  value="Delete category" onclick="deleRow()" id="btnDele' + i + '" />';
      return false;
  }

  //delete row
  function deleRow() {
      var cGetRow = window.event.srcElement.parentElement.parentElement.rowIndex;
      myTable.deleteRow(cGetRow);
      return false;
  }

  //
  function saveTableValue() {

      var myTable = document.getElementById("myTable");

      tableValue="";
      for (var i=1;i<myTable.rows.length;i++){
          var value1 = myTable.rows[i].cells[0].getElementsByTagName("input")[0].value;//
          var value2 = myTable.rows[i].cells[1].getElementsByTagName("input")[0].value;//
          var rowValue=value1+"_"+value2; //
          tableValue=tableValue+rowValue+"+";
      }

      $("#USECATTYPENUM").val(tableValue);//
  }
