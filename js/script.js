let csv_rows = {};
let preds = {};

window.addEventListener('load', () => {
    let updform = document.getElementById('update-form');
    let insform = document.getElementById('insert-form');
    let pred = document.getElementById('predictions');
    let csv_label = document.querySelector('#csv-upload');
    
    csv_label.addEventListener('change', e => {
      e.preventDefault();
      let file_path = updform.elements['csv'].value;
      let val = file_path.slice(file_path.lastIndexOf('\\')+1, file_path.length);
      document.querySelector('span.csv-update-item').textContent = val;
    });
    
    updform.addEventListener('submit', (e) => {
    
      e.preventDefault();
      
      let req = new XMLHttpRequest();
      req.onreadystatechange = () => {
        if(req.readyState == 4 && req.status == 200){
          let data = JSON.parse(req.responseText);
          console.log(data);
          
          let s = "";
          for(let i in data){
            let obj = data[i];
            csv_rows[i] = obj['csv_row'];
            console.log(i);
            s += `
              <div class='student-selection'>
                <div class='student-csv-data'>
                  <div class='student-csv-data-item'>(`+i+`)</div>
                  <div class='student-csv-data-item'>`+obj['csv_row']['fname']+`</div>
                  <div class='student-csv-data-item'>`+obj['csv_row']['lname']+`</div>
                  <div class='student-csv-data-item'>`+obj['csv_row']['dob']+`</div>
                  <div class='student-csv-data-item'>`+obj['csv_row']['marks']+`</div>
                </div>
                <div class='student-predictions>'
            `;
            
            for(let j in obj['predictions']){
              let pred = obj['predictions'][j];
              preds[i+'-'+j] = pred;
              preds[i+'-'+j]['csv_row'] = i;
              s += `
                <div class='student-pred-data'>
                  <input type='radio' class='student-pred-data-item radio-submits' name='radio`+i+`' id=`+i+'-'+j+`>
                  <div class='student-pred-data-item'>`+pred['fname']+`</div>
                  <div class='student-pred-data-item'>`+pred['lname']+`</div>
                  <div class='student-pred-data-item'>`+pred['dob']+`</div>
                  <div class='student-pred-data-item'>`+pred['marks']+`</div>
                </div>
              `;
            }
            
            s+='</div></div>';
          }
          
          pred.innerHTML = s;
          document.querySelector('span.csv-update-item').textContent = '';
        }
      };
      
      req.open('POST', '/project/api/csv/', true);
      req.send(new FormData(updform));
      
    });
    
    insform.addEventListener('submit', (e) => {
      e.preventDefault();
      
      let req = new XMLHttpRequest();
      req.onreadystatechange = () => {
        if(req.readyState == 4 && req.status == 200){
          console.log(req.responseText);
        }
      }
      
      let data = {};
      let formdata = new FormData(insform);
      for(let [k, v] of formdata.entries())
        data[k] = v;
      
      req.open('POST', '/project/api/create/', true);
      req.setRequestHeader("Content-Type", "application/json");
      req.send(JSON.stringify(data));
      
      for(let item of insform.elements)
        item.value = '';
      
    });
    
    document.getElementById('update-submit').addEventListener('click', (e) => {
      e.preventDefault();
      let checked_radios = document.querySelectorAll('.radio-submits:checked');
      
      if(checked_radios.length != Object.keys(csv_rows).length){
        alert('Select updates for all the CSV rows.');
        return;
      }
      
      let data = [];
      
      for(let radio of checked_radios){
        let row = preds[radio.id].csv_row;
        data.push({
          'id' : preds[radio.id].id,
          'fname' : csv_rows[row].fname,
          'lname' : csv_rows[row].lname,
          'marks' : csv_rows[row].marks,
          'dob' : csv_rows[row].dob
        });
      }  
      
      let req = new XMLHttpRequest();
      req.onreadystatechange = () => {
        if(req.readyState == 4 && req.status == 200){
          console.log(data);
        }
      }
      
      req.open('POST', '/project/api/mult_update/', true);
      req.send(JSON.stringify(data));
      
    });

});