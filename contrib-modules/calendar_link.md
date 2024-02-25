composer require spatie/calendar-links
composer require drupal/calendar_link

// used in bankruptcy
<div class="events-top-publish">
    <div class="right-icon">
		{# Add to Calendar button dropdown #}
	    {% if content.field_date['#items'] %}
			{# convert start and end date to HTML time to accept it #}
			{% set cal_start_date = node.field_date.value %}
			{% set cal_start_date = '<time datetime="' ~ cal_start_date|date("c") ~ '" data-drupal-timezone="user">' ~ cal_start_date|date('d/m/Y') ~ '</time>' %}
		    {% set cal_end_date = node.field_date.end_value %}
			{% set cal_end_date = '<time datetime="' ~ cal_end_date|date("c") ~ '" data-drupal-timezone="user">' ~ cal_end_date|date('d/m/Y') ~ '</time>' %}
			{% set placeName = 'demo' %}
			{% set calendarLinks = calendar_links(node.title.value, cal_start_date, cal_end_date, 1, node.body.summary, placeName) %}
			<div class="details-item">
				<div class="dropdown">
					<a class="botton-events" type="button" id="calendarDropdown" data-bs-toggle="dropdown" aria-expanded="false">
						{{ 'Add to calendar'|t }}
					</a>
					<ul class="dropdown-menu calendarList" aria-labelledby="calendarDropdown">
						{% for link in calendarLinks %}
						<li>
						    <a href="{{ link.url }}" class="calendar-link-{{ link.type_key }}" target="{{ link.type_key == 'ics' ? '' : '_blank' }}">{{ link.type_key == 'ics' ? 'Apple' : link.type_name }}</a>
						</li>
					    {% endfor %}
					</ul>
			    </div>
		    </div>
		{% endif %}
    </div>
</div>