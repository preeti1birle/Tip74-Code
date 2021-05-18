<h3>Image</h3>
<form method="post" enctype="multipart/form-data" action="<?php echo BASE_URL; ?>upload/image">
	<p><input type="file" name="File"/></p>
	<p>SessionKey: <input type="text" name="SessionKey" value="8fe59ea0-9fde-485c-2b1c-ee951fd69c28" style="width:250px;"></p>
	<p>Section: 
		<select name="Section">
			<option value="ProfilePic">ProfilePic</option>
			<option value="ProfileCoverPic">ProfileCoverPic</option>
			<option value="Post">Post</option>
			<option value="Message">Message</option>
			<option value="Category">Category</option>
			<option value="Product">Product</option>
			<option value="Coupon">Coupon</option>
			<option value="Store">Store</option>
			<option value="Group">Group</option>
			<option value="GroupCover">GroupCover</option>
			<option value="StoreCover">StoreCover</option>
			<option value="Broadcast">Broadcast</option>
			<option value="Event">Event</option>
		</select>
	</p>
	<p><input type="submit" name="uploadFile" value="UPLOAD"/></p>
</form>

<h3>File</h3>
<form method="post" enctype="multipart/form-data" action="<?php echo BASE_URL; ?>upload/file">
	<p><input type="file" name="File"/></p>
	<p>SessionKey: <input type="text" name="SessionKey" value="8fe59ea0-9fde-485c-2b1c-ee951fd69c28" style="width:250px;"></p>
	<p>Section: 
		<select name="Section">
			<option value="File">File</option>
		</select>
	</p>
	<p><input type="submit" name="uploadFile" value="UPLOAD"/></p>
</form>