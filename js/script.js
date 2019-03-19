let csv_rows = {};
let preds = {};

window.addEventListener('load', () => {
    // Handle to the CSV Upload form
    let updform = document.querySelector('#update-form');
    
    // Handle to the Insertion form
    let insform = document.querySelector('#insert-form');
    
    // Handle to the data display div
    let pred = document.querySelector('#predictions');
    
    // Handle to the CSV Input 
    let csv_label = document.querySelector('#csv-upload');
    
    // Handle to the Clear button
    let clear_data = document.querySelector('#clear-data');
    
    // Handle to the suggestion submit button
    let update_submit = document.querySelector('#update-submit');
    
    // Handle to the Show All students button
    let show_all_students = document.querySelector('.show-all-students-button');


    // Displays the name of the file when selected
    csv_label.addEventListener('change', e => {
      e.preventDefault();
      let file_path = updform.elements['csv'].value;
      let val = file_path.slice(file_path.lastIndexOf('\\')+1, file_path.length);
      document.querySelector('span.csv-update-item').textContent = val;
    });
    
    
    // Renders the suggestions on CSV upload
    updform.addEventListener('submit', (e) => {
      e.preventDefault();
      
      let req = new XMLHttpRequest();
      req.onreadystatechange = () => {
        if(req.readyState == 4){
          if(req.status == 200){
            let data = JSON.parse(req.responseText);

            let s = "";
            for(let i in data){
              let obj = data[i];
              csv_rows[i] = obj['csv_row'];
              console.log(i);
              s += `
                <div class='student-selection'>
                  <div class='student-selection-inner'>
                    <div class='student-csv-data'>
                      <div class='student-csv-data-inner'>
                        <div class='student-csv-data-item'><div class='student-csv-data-item-inner'>`+i+`.</div></div>
                        <div class='student-csv-data-item'><div class='student-csv-data-item-inner'>`+obj['csv_row']['fname']+`</div></div>
                        <div class='student-csv-data-item'><div class='student-csv-data-item-inner'>`+obj['csv_row']['lname']+`</div></div>
                        <div class='student-csv-data-item'><div class='student-csv-data-item-inner'>`+obj['csv_row']['dob']+`</div></div>
                        <div class='student-csv-data-item'><div class='student-csv-data-item-inner'>`+obj['csv_row']['marks']+`</div></div>
                      </div>
                    </div>
                    <div class='student-predictions'>
                      <div class='student-predictions-inner'>
              `;
              
              for(let j in obj['predictions']){
                let pred = obj['predictions'][j];
                preds[i+'-'+j] = pred;
                preds[i+'-'+j]['csv_row'] = i;
                s += `
                  <div class='student-pred-data'>
                    <div class='student-pred-data-inner'>
                      <div class='student-pred-data-item'>  
                        <input type='radio' class='radio-submits' name='radio`+i+`' id=`+i+'-'+j+`>
                      </div>
                      <div class='student-pred-data-item'>`+pred['fname']+`</div>
                      <div class='student-pred-data-item'>`+pred['lname']+`</div>
                      <div class='student-pred-data-item'>`+pred['dob']+`</div>
                      <div class='student-pred-data-item'>`+pred['marks']+`</div>
                    </div>
                  </div>
                `;
              }
              
              s+='</div></div></div></div>';
            }
            
            pred.innerHTML = s;
            update_submit.style.display = clear_data.style.display = 'block';
            alert('Matched rows updated; select the rows to be updated from the shown suggestions.');
            
          } else if (req.status == 400) {
            alert('Uploaded file not CSV');
          } else if (req.status == 500) {
            alert('Internal Server Error');
          } 
        }
      };
      
      req.open('POST', '/project/api/csv/', true);
      req.send(new FormData(updform));
      
    });
    
    
    // POSTs form data to API on click
    insform.addEventListener('submit', (e) => {
      e.preventDefault();
      
      let req = new XMLHttpRequest();
      req.onreadystatechange = () => {
        if(req.readyState == 4){
          if(req.status == 200)
            alert('Successfully inserted student.');
          else if(req.status == 500)
            alert('Internal Server Error');
          else if(req.status == 400)
            alert('Insert acceptable values.');
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
    
    
    // POSTs the selected suggestions to the API
    update_submit.addEventListener('click', (e) => {
      e.preventDefault();
      
      // Gets all the selected suggestions
      let checked_radios = document.querySelectorAll('.radio-submits:checked');
      
      if(checked_radios.length != Object.keys(csv_rows).length){
        alert('Select updates for all the CSV rows.');
        return;
      }
      
      let data = [];
      
      for(let radio of checked_radios){
        
        // The unmatched CSV row this suggestion corresponds to
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
        if(req.readyState == 4){
          if(req.status == 200)
            alert('Updated the rows'); 
          else if (req.status == 400) {
            alert("Marks need to be in 0-100 and don't meddle with the shown data");
          } else if (req.status == 500) {
            alert('Internal Server Error');
          } 
        }
      }
      
      req.open('POST', '/project/api/mult_update/', true);
      req.send(JSON.stringify(data));
      
    });
    
    
    // Clears the Data Display div and hides the Clear and Update button
    clear_data.addEventListener('click', e => {
      pred.innerHTML = '';
      update_submit.style.display = clear_data.style.display = 'none';
    });
    
    
    // GETs all the students and renders them in the Data Display div
    show_all_students.addEventListener('click', e => {
      e.preventDefault();
      
      let req = new XMLHttpRequest();
      req.onreadystatechange = () => {
        if(req.readyState == 4){
          if(req.status == 200){
            let data = JSON.parse(req.responseText);
            let html = `
              <div class='student-data-header'>
                <div class='student-data-header-inner'>
                  <div class='student-data-header-item'>First Name</div>
                  <div class='student-data-header-item'>Last Name</div>
                  <div class='student-data-header-item'>DOB</div>
                  <div class='student-data-header-item'>Marks</div>
                </div>
              </div>
            `;
            for(let obj of data){
              html += `
                <div class='student-data'>
                  <div class='student-data-inner'>
                    <div class='student-data-item'>`+obj['fname']+`</div>
                    <div class='student-data-item'>`+obj['lname']+`</div>
                    <div class='student-data-item'>`+obj['dob']+`</div>
                    <div class='student-data-item'>`+obj['marks']+`</div>
                  </div>
                </div>
              `;
            }
            
            pred.innerHTML = html;
            clear_data.style.display = 'block';
          }
          else
            alert('Internal Server Error');
        }
      }
      
      req.open('GET', '/project/api/read/?type=all', true);
      req.send();
      
    });

});