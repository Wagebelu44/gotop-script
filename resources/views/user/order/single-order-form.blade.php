<div id="vueHolder">
    <form action="{{url('make_new_order')}}" method="post"  id="order-form-gotop" class="has-validation-callback">
       @csrf
     {{--   <div class="form-group">
          <label for="orderform-category">Category:</label>
          @php
               $selected = null;
              if ($order != null) {
                  $selected = $order->category_id;
              }
          @endphp
          <select class="form-control" id="category_id" name="category_id" @change="categoryChanges">
             <option value="" >Select a Category</option>
             @foreach($categories as $key => $category)
             <option value="{{$category->id}}" {{($selected !=null)?($selected == $category->id?'selected':''):($key == 0?'selected':'')}}  >{{$category->name}}</option>
             @endforeach
          </select>
       </div> --}}
       <div class="form-group">
       <label for="orderform-category">Category:</label>
          <select class="form-control" id="category_id" name="category_id" @change="categoryChanges">
            <option value="" >Select a Category</option>
            <option :value="c.id" v-for="(c,i) in categoryjs">@{{c.name}}</option>
          </select>
       </div>
       <div class="form-group">
          <label for="orderform-service">Service: </label>
          <select class="form-control" id="service_id" name="service_id" v-model="service_id">
             <div v-if="services.length !== 0">
                <option v-for="serv in services" :value="serv.id">@{{serv.display_name}} {{-- @{{ serv.id }} @{{ serv.name }} - $@{{ serv.price }} --}} </option>
             </div>
          </select>
          <input type="hidden" name="service_mode" v-model="service_mode" >
       </div>
       <div class="description hidden fields" id="service_description">
          <div class="service-description-split"></div>
       </div>
       <div class="description">
          <div class="form-group">
             <label for="description" class="control-label">Details: 📝 (Read before order)</label>
             <div v-html="serviceDesc" readonly style="height: 250px; overflow-y: scroll"  class="form-control"></div>
             {{-- <textarea readonly name="description"  id="description" rows="5" style="height: 250px"  class="form-control"></textarea> --}}
          </div>
       </div>
       <div class="form-group  fields" id="order_link">
          <label class="control-label" for="field-orderform-fields-link">Link: 📌 (Must be public) </label>
          <input class="form-control" name="link" value="" type="text" id="link">
       </div>
       {{-- all the optional fields --}}
       <div class="description" v-if="comments_visible">
          <div class="form-group">
             <label for="comments" class="control-label">Comments: (1 per line)</label>
             <textarea  name="text_area_1" @keydown='countPerLine' id="comments" rows="5" style="height: 250px" class="form-control" placeholder="comments"></textarea>
          </div>
       </div>
       <div class="description" v-if="keyword_visible">
          <div class="form-group">
             <label for="keywords" class="control-label">Keywords: (1 per line)</label>
             <textarea  name="text_area_1" @keydown='countPerLine' id="keywords" rows="5" style="height: 250px" class="form-control" placeholder="Key words"></textarea>
          </div>
       </div>
       <div class="description" v-if="perline_username_visible">
          <div class="form-group">
             <label for="keywords" class="control-label">Username: (1 per line)</label>
             <textarea  name="text_area_1" @keydown='countPerLine' id="per_line_username" rows="5" style="height: 250px" class="form-control" placeholder="Username per line"></textarea>
          </div>
       </div>
       <div class="description" v-if="hastags_visible">
          <div class="form-group">
             <label for="hastags" class="control-label">Hastags: (1 per line)</label>
             <textarea  name="text_area_2" id="hastags" rows="5" style="height: 250px" class="form-control" placeholder="Hastags"></textarea>
          </div>
       </div>
       <div class="form-group  fields" v-if="additional_username_visible" >
          <label class="control-label" for="field-orderform-fields-link">Username: </label>
          <input class="form-control" name="additional_inputs" value="" type="text" id="username">
       </div>
       <div class="form-group  fields" v-if="additional_email_visible">
          <label class="control-label" for="field-orderform-fields-link">Email: </label>
          <input class="form-control" name="additional_inputs" value="" type="text" id="email">
       </div>
       <div class="form-group  fields" v-if="additional_comment_owner_username_visible">
          <label class="control-label" for="field-orderform-fields-link">Username of the comment owner </label>
          <input class="form-control" name="additional_inputs" value="" type="text" id="owner_username">
       </div>
       <div class="form-group  fields"  v-if="additional_hashtags_visible">
          <label class="control-label" for="field-orderform-fields-link">Hashtag</label>
          <input class="form-control" name="additional_inputs" value="" type="text" id="hashtag">
       </div>
       <div class="form-group  fields" v-if="additional_media_url_visible">
          <label class="control-label" for="field-orderform-fields-link">Media URL</label>
          <input class="form-control" name="additional_inputs" value="" type="text" id="media_url">
       </div>
       <div class="form-group  fields" v-if="additional_answer_number_visible" >
          <label class="control-label" for="field-orderform-fields-link">Answer Number</label>
          <input class="form-control" name="additional_inputs" value="" type="text" id="answer_number">
       </div>
       {{-- all the optional fields ends --}}
       <div class="form-group  fields" id="order_quantity" v-if="quantity_visibility">
          <label class="control-label" for="field-orderform-fields-quantity">Quantity: 🔢</label>
          <input class="form-control" :class="{ 'input-disabled' : inputDisable}" name="quantity" v-model="quantity" type="number">
       </div>
       <span v-if="!quantity_visibility">
         <input  name="quantity" v-model="quantity" type="hidden">
       </span>
       <div class="order_quantity_validation" v-if="quantity_validation">
            <p class="text-danger">@{{quantity_validation_msg}}</p>
       </div>
       {{--        drip feeed--}}
       <div class="form-group" id="drip_feed" v-if="drip_feed_available">
          <input type="checkbox" v-model="drip_feed" id="exampleCheck1"  name="drip_feed">
          <label class="form-check-label" for="exampleCheck1">Drip-feed</label>
       </div>
       <div id="drip_field" class="" v-if="drip_feed_content">
          <div class="form-group  fields" id="order_runs">
             <label class="control-label" for="field-orderform-fields-runs">Runs: </label>
             <input class="form-control" name="runs" v-model="runs" type="number">
          </div>
          <div class="form-group  fields" id="order_interval">
             <label class="control-label" for="field-orderform-fields-interval">Interval (minutes): </label>
             <input class="form-control" name="interval" v-model="interval"  type="number">
          </div>
          <div class="form-group  fields" id="order_total_quantity">
             <label class="control-label" for="field-orderform-fields-total_quantity">Total quantity: </label>
             <input class="form-control" readonly name="total_quantity"  v-model="total_quantity" type="number" >
          </div>
       </div>
       {{--            end drip feed--}}
       <div class="form-group">
          <div class="card card-mini price bg-light">
             <div class="card-body">
                <span class="card-text">
                <b>Charge: 💰</b></span>
                <p>$<span id="order_total">@{{ charge }}</span></p>
                <input type="hidden" name="charge" :value="charge">
                <p id="not-enough-funds" style="display:none;color:red">Order amount exceeds available funds</p>
             </div>
          </div>
          <span v-if="current_service_type !== 'Package'">
             <small class="help-block min-max">Min: <span id="min-q">@{{min}}</span> - Max: <span id="max-q">@{{max}}</span></small>
          </span>
       </div>
       <div class="form-group" id="custom-comments-div" style="display: none">
          <label for="custom_comments" class="control-label">Custom Data</label>
          <textarea class="form-control" id="custom_comments" style="height: 150px;" placeholder="1 on each line" name="custom_comments"></textarea>
       </div>
       <div class="form-group">
          <button class="btn btn-primary btn-block" id="btn-proceed" type="submit">Submit</button>
       </div>
    </form>
 </div>

