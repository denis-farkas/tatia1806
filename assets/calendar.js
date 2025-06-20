import { Calendar } from "@fullcalendar/core";
import interactionPlugin from "@fullcalendar/interaction";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import listPlugin from "@fullcalendar/list";
import frLocale from "@fullcalendar/core/locales/fr";

import "./styles/calendar.css";

document.addEventListener("DOMContentLoaded", () => {
  const calendarEl = document.getElementById("calendar-holder");

  // IMPORTANT: Guard clause to prevent errors if element doesn't exist
  if (!calendarEl) {
    console.warn("Calendar element #calendar-holder not found on page");
    return; // Exit early if calendar container doesn't exist
  }

  // Use the route from your controller
  const eventsUrl = "/load-events";

  try {
    const calendar = new Calendar(calendarEl, {
      initialView: "timeGridWeek", // Start with week view for better course visibility
      editable: false,
      events: eventsUrl, // Simplified events URL configuration
      headerToolbar: {
        left: "prev,next today",
        center: "title",
        right: "dayGridMonth,timeGridWeek,timeGridDay,listWeek",
      },
      timeZone: "Europe/Paris", // Changed to Paris timezone for French locale
      plugins: [interactionPlugin, dayGridPlugin, timeGridPlugin, listPlugin],
      locale: frLocale,
      weekends: true,
      businessHours: [
        {
          daysOfWeek: [1, 2, 3, 4, 5, 6, 7],
          startTime: "09:00",
          endTime: "12:00",
        },
        {
          daysOfWeek: [1, 2, 3, 4, 5, 6, 7],
          startTime: "13:00",
          endTime: "19:00",
        },
        {
          daysOfWeek: [1, 2, 3, 4, 5, 6, 7],
          startTime: "20:00",
          endTime: "22:00",
        },
      ],
      selectConstraint: "businessHours",
      slotMinTime: "09:00:00",
      slotMaxTime: "22:00:00",
      validRange: {
        start: new Date().toISOString().split("T")[0],
      },
      // Simplified event styling for courses
      eventDidMount: function (info) {
        // Add tooltip with course information
        const tooltipContent = `
          <div>
            <strong>${info.event.title}</strong><br>
            Début: ${new Date(info.event.start).toLocaleTimeString("fr-FR", {
              hour: "2-digit",
              minute: "2-digit",
            })}<br>
            Fin: ${new Date(info.event.end).toLocaleTimeString("fr-FR", {
              hour: "2-digit",
              minute: "2-digit",
            })}
          </div>
        `;

        // Use browser title for tooltip
        info.el.title = info.event.title;

        // Optional: you can add click handler for more details
        info.el.style.cursor = "pointer";
      },
      eventClick: function (info) {
        // Legend: title, salle, description
        alert(
          "Cours : " +
            info.event.title +
            "\nSalle : " +
            (info.event.extendedProps.salle || "") +
            "\nDescription : " +
            (info.event.extendedProps.description || "")
        );
      },
    });

    // Render the calendar
    calendar.render();

    // Simplified legend for courses only
    const existingLegend = document.querySelector(".calendar-legend");
    if (!existingLegend) {
      const legendEl = document.createElement("div");
      legendEl.className = "calendar-legend";
      legendEl.innerHTML = `
        <div class="legend-item">
          <span class="color-box" style="background-color: #3788d8;"></span>
          <span>Cours disponibles</span>
        </div>
      `;

      // Only insert legend if parent node exists
      if (calendarEl.parentNode) {
        calendarEl.parentNode.insertBefore(legendEl, calendarEl);
      }
    }
  } catch (error) {
    console.error("Error initializing calendar:", error);
  }
});
