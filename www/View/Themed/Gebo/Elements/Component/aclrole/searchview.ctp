<?php 
        $default['name'] = empty($default['name']) ? '' : $default['name']; 
?>
                                        <div class="row-fluid">
						<div class="span12">
							<div class="box box-color box-bordered">
								<div class="box-title">
									<h3 class="heading"><!--<i class="icon-th-list"></i>-->
									<?php echo __('ค้นหา') . ' ' . __('ข้อมูลสิทธิ์ผู้ใช้งาน') ;?>
									</h3>
								</div>
                                                                
								<div class="box-content nopadding">
									<form  action="../../AclRoles" method="POST" class='form-horizontal form-bordered' enctype='multipart/form-data'>
										<input  type="hidden" name="offset" value="0" />
										<div class="control-group">
											<div class="span5">
												<label for="textfield" class="control-label"><?php echo __('ชื่อ');?> : </label>
												<div class="controls">
													<div class="input-xlarge">
														<input name="name" type="text" value="<?php echo $default['name']; ?>" class='complexify-me input-block-level' placeholder="<?php echo __('ชื่อ');?>" onkeypress="keyTextThai()">
													</div>
												</div>
											</div>
											<div class="span1">
											<button id="<?php echo $pagination['model']; ?>-submit" type="submit" class="btn btn-primary"><?php echo __('ค้นหา');?></button>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>