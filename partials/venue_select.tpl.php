<tr class="form-field form-required">
		<th scope="row">
		  <label for="pass2">Repeat Password <span class="description">(required)</span></label>
		</th>
		<td>
		  <select name="venue_id">
		    <?php foreach ($venues as $venue): ?>
		      <option value="<?php echo $venue->id ?>"><?php echo $venue->name ?></option>
		    <?php endforeach; ?>
		  </select>
		</td>
</tr>

