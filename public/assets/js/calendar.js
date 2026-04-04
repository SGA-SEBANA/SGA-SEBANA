console.log("calendar.js cargado");
document.addEventListener("DOMContentLoaded", function () {
  const calendarEl = document.getElementById("calendar");

  if (!calendarEl) return;

  const today = new Date();
  const tomorrow = new Date(today);
  tomorrow.setDate(today.getDate() + 1);

  const nextWeek = new Date(today);
  nextWeek.setDate(today.getDate() + 7);

  const events = [
    {
      title: "Team Meeting",
      start: "2025-01-25T09:00:00",
      end: "2025-01-25T10:30:00",
      backgroundColor: "#007bff",
      borderColor: "#007bff",
      textColor: "#ffffff",
      extendedProps: {
        type: "meeting",
        description: "Weekly team sync meeting",
      },
    },

    {
      title: "Project Deadline",
      start: "2025-01-28",
      allDay: true,
      backgroundColor: "#dc3545",
      borderColor: "#dc3545",
      textColor: "#ffffff",
      extendedProps: {
        type: "deadline",
        description: "Final submission deadline",
      },
    },

    {
      title: "Client Presentation",
      start: "2025-02-02T14:00:00",
      end: "2025-02-02T15:30:00",
      backgroundColor: "#17a2b8",
      borderColor: "#17a2b8",
      textColor: "#ffffff",
      extendedProps: {
        type: "presentation",
        description: "Quarterly review presentation",
      },
    },

    {
      title: "Doctor Appointment",
      start: "2025-01-30T11:00:00",
      end: "2025-01-30T12:00:00",
      backgroundColor: "#ffc107",
      borderColor: "#ffc107",
      textColor: "#000000",
      extendedProps: {
        type: "appointment",
        description: "Annual health checkup",
      },
    },

    {
      title: "Development Task",
      start: "2025-01-27T10:00:00",
      end: "2025-01-27T16:00:00",
      backgroundColor: "#28a745",
      borderColor: "#28a745",
      textColor: "#ffffff",
      extendedProps: {
        type: "task",
        description: "Feature implementation",
      },
    },

    {
      title: "Conference Call",
      start: "2025-02-05T15:00:00",
      end: "2025-02-05T16:00:00",
      backgroundColor: "#007bff",
      borderColor: "#007bff",
      textColor: "#ffffff",
      extendedProps: {
        type: "meeting",
        description: "International team sync",
      },
    },
  ];

  const calendar = new FullCalendar.Calendar(calendarEl, {
    locale: "es", //idioma
    buttonText: {
      today: "Hoy",
      month: "Mes",
      week: "Semana",
      day: "Día",
      list: "Lista",
    },
    initialView: "dayGridMonth",

    // cambios manuales

    allDayText: "Todo el día",

    noEventsText: "No hay eventos para mostrar",

    headerToolbar: {
      left: "prev,next today",
      center: "title",
      right: "dayGridMonth,timeGridWeek,timeGridDay,listWeek",
    },

    height: "auto",

    events: "/SGA-SEBANA/public/admin/visit-calendar-events",

    eventDisplay: "block",

    dayMaxEvents: 3,

    // cambios manuales

    moreLinkText: "más",

    eventClick: function (info) {
      const event = info.event;
      const props = event.extendedProps;

      alert(
        `Evento: ${event.title}\n` +
          `Tipo: ${props.type || "N/A"}\n` +
          `Descripción: ${props.description || "Sin descripción"}\n`,
      );
    },

    dateClick: function (info) {
      const title = prompt("Enter event title:");

      if (title) {
        calendar.addEvent({
          title: title,
          start: info.date,
          allDay: info.allDay,
          backgroundColor: "#6c757d",
          borderColor: "#6c757d",
          textColor: "#ffffff",
        });
      }
    },

    windowResize: function () {
      calendar.updateSize();
    },
  });

  calendar.render();

  window.calendarInstance = calendar;
});

function addNewEvent() {
  const title = prompt("Enter event title:");

  if (title && window.calendarInstance) {
    const today = new Date();

    window.calendarInstance.addEvent({
      title: title,
      start: today,
      backgroundColor: "#6c757d",
      borderColor: "#6c757d",
      textColor: "#ffffff",

      extendedProps: {
        type: "custom",
        description: "User created event",
      },
    });
  }
}
